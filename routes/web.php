<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PhoneNumberController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\TravelInsuranceController;
use App\Http\Controllers\CvBuilderController;
use App\Http\Controllers\FlightBookingController;
use App\Http\Controllers\FlightRequestController;
use App\Http\Controllers\HotelBookingController;
// use App\Http\Controllers\VisaApplicationController; // Removed - use bgproject's tourist-visa system
use App\Http\Controllers\WalletController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\AdminJobPostingController;
use App\Http\Controllers\Admin\AdminJobApplicationController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminAnalyticsController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\HotelController as AdminHotelController;
use App\Http\Controllers\Admin\VisaController as AdminVisaController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// PWA Routes
Route::get('/offline', function () {
    return Inertia::render('Offline');
})->name('offline');

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

// Public settings API endpoint
Route::get('/public/settings', function () {
    return response()->json([
        'app_name' => config('app.name', 'BideshGomon'),
        'app_url' => config('app.url'),
        'currency' => 'BDT',
        'timezone' => config('app.timezone'),
    ]);
});

// Short-circuit protected profile assessment JSON endpoints to ensure they are reachable
// (these are still protected by 'auth' middleware)
Route::middleware('auth')->get('/profile/assessment/recommendations', [\App\Http\Controllers\ProfileAssessmentController::class, 'recommendations'])
    ->name('profile.assessment.recommendations');
Route::middleware('auth')->get('/profile/assessment/score-breakdown', [\App\Http\Controllers\ProfileAssessmentController::class, 'scoreBreakdown'])
    ->name('profile.assessment.score-breakdown');

// Role-based dashboard routing
Route::get('/dashboard', function () {
    $user = auth()->user();
    
    // Redirect to appropriate dashboard based on role
    if ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }
    
    if ($user->hasRole('agency')) {
        return redirect()->route('agency.dashboard');
    }
    
    if ($user->hasRole('consultant')) {
        return redirect()->route('consultant.dashboard');
    }
    
    // Default: User dashboard
    $profile = $user->userProfile;
    
    // Calculate stats
    $stats = [
        'education_count' => $user->educations()->count(),
        'experience_count' => $user->workExperiences()->count(),
        'family_count' => $user->familyMembers()->count(),
        'languages_count' => $user->languages()->count(),
        'profile_strength' => $user->educations()->count() > 0 && $user->workExperiences()->count() > 0 ? 'Strong' : 'Basic',
    ];
    
    // Calculate profile completion using User model method (12-section system)
    $completion = $user->calculateProfileCompletion();
    
    // Load active services from database for dashboard
    $activeServices = [];
    try {
        if (class_exists('App\Models\ServiceModule')) {
            // Map slugs to actual routes
            $routeMapping = [
                'ai-profile-assessment' => 'profile.assessment.show',
                'wallet' => 'wallet.index',
                'referrals' => 'referral.index',
                'profile-edit' => 'profile.edit',
                'education' => 'profile.edit',
                'work-experience' => 'profile.edit',
                'passports' => 'profile.passports.index',
                'travel-history' => 'profile.travel-history.index',
                'languages' => 'profile.edit',
                'family' => 'profile.edit',
                'support' => 'support.index',
                'appointments' => 'appointments.index',
                'faqs' => 'faqs.index',
                'events' => 'events.index',
                'document-scanner' => 'document-scanner.index',
                'cv-builder' => 'cv-builder.index',
                'public-profile' => 'profile.public.settings',
                'travel-insurance' => 'travel-insurance.index',
                'flight-requests' => 'flight-requests.index',
                'payments' => 'payments.index',
                'financial' => 'profile.edit',
                'security' => 'profile.edit',
                'visa-history' => 'profile.visa-history.index',
            ];
            
            $activeServices = \App\Models\ServiceModule::with('category')
                ->where('is_active', true)
                ->where('coming_soon', false)
                ->orderBy('is_featured', 'desc')
                ->orderBy('sort_order')
                ->get()
                ->map(function ($service) use ($routeMapping) {
                    $route = $routeMapping[$service->slug] ?? null;
                    $params = null;
                    
                    // Handle profile.edit with section params
                    if ($route === 'profile.edit') {
                        if ($service->slug === 'education') {
                            $params = ['section' => 'education'];
                        } elseif ($service->slug === 'work-experience') {
                            $params = ['section' => 'experience'];
                        } elseif ($service->slug === 'languages') {
                            $params = ['section' => 'languages'];
                        } elseif ($service->slug === 'family') {
                            $params = ['section' => 'family'];
                        } elseif ($service->slug === 'financial') {
                            $params = ['section' => 'financial'];
                        } elseif ($service->slug === 'security') {
                            $params = ['section' => 'security'];
                        }
                    }
                    
                    return [
                        'id' => $service->id,
                        'name' => $service->name,
                        'slug' => $service->slug,
                        'description' => $service->short_description ?? $service->full_description,
                        'icon' => $service->icon ?? 'document',
                        'is_featured' => $service->is_featured,
                        'category' => $service->category->name ?? 'Other',
                        'route' => $route,
                        'route_params' => $params,
                    ];
                })->toArray();
        }
    } catch (\Exception $e) {
        \Log::warning('Failed to load services: ' . $e->getMessage());
    }
    
    // Get leaderboard data
    $topReferrers = [];
    $userRank = null;
    
    try {
        if (class_exists('App\Models\Referral')) {
            $period = now()->format('Y-m');
            [$year, $month] = explode('-', $period);
            $startDate = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = \Carbon\Carbon::create($year, $month, 1)->endOfMonth();
            
            // Get top 5 referrers for this month
            $topReferrers = \App\Models\Referral::select('referrer_id', \Illuminate\Support\Facades\DB::raw('COUNT(*) as referral_count'), \Illuminate\Support\Facades\DB::raw('SUM(reward_amount) as total_earnings'))
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed')
                ->groupBy('referrer_id')
                ->orderByDesc('referral_count')
                ->orderByDesc('total_earnings')
                ->limit(5)
                ->get()
                ->map(function ($item, $index) {
                    $referrer = \App\Models\User::find($item->referrer_id);
                    return [
                        'rank' => $index + 1,
                        'user_id' => $item->referrer_id,
                        'user' => [
                            'id' => $referrer->id ?? null,
                            'name' => $referrer->name ?? 'Unknown',
                            'email' => $referrer->email ?? null,
                        ],
                        'referral_count' => (int) $item->referral_count,
                        'total_earnings' => (float) $item->total_earnings,
                    ];
                });
            
            // Get user's rank
            $allReferrers = \App\Models\Referral::select('referrer_id', \Illuminate\Support\Facades\DB::raw('COUNT(*) as referral_count'), \Illuminate\Support\Facades\DB::raw('SUM(reward_amount) as total_earnings'))
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'completed')
                ->groupBy('referrer_id')
                ->orderByDesc('referral_count')
                ->orderByDesc('total_earnings')
                ->get();
            
            $userRankIndex = $allReferrers->search(function ($item) use ($user) {
                return $item->referrer_id == $user->id;
            });
            
            if ($userRankIndex !== false) {
                $userData = $allReferrers[$userRankIndex];
                $userRank = [
                    'rank' => $userRankIndex + 1,
                    'referral_count' => (int) $userData->referral_count,
                    'total_earnings' => (float) $userData->total_earnings,
                ];
            }
        }
    } catch (\Exception $e) {
        // Silently fail if referral system not available
    }
    
    // Generate smart suggestions based on profile completion
    $suggestions = [];
    
    // Always show Profile Assessment as first suggestion if profile exists
    if ($profile) {
        $suggestions[] = [
            'title' => 'AI Profile Assessment',
            'description' => 'Get personalized recommendations and improve your profile strength',
            'icon' => 'sparkles',
            'priority' => 'high',
            'route' => 'profile.assessment.show',
        ];
    }
    
    if (!$profile || !$profile->phone) {
        $suggestions[] = [
            'title' => 'Add Contact Information',
            'description' => 'Complete your phone and address details to receive application updates',
            'icon' => 'phone',
            'priority' => 'high',
            'route' => 'profile.edit',
        ];
    }
    
    if (!$profile || !$profile->passport_number) {
        $suggestions[] = [
            'title' => 'Add Passport Details',
            'description' => 'Required for visa and travel applications',
            'icon' => 'document',
            'priority' => 'high',
            'route' => 'profile.edit',
        ];
    }
    
    if ($user->educations()->count() === 0) {
        $suggestions[] = [
            'title' => 'Add Education History',
            'description' => 'Improve job matches and visa approval chances',
            'icon' => 'academic',
            'priority' => 'medium',
            'route' => 'profile.edit',
        ];
    }
    
    if ($user->workExperiences()->count() === 0) {
        $suggestions[] = [
            'title' => 'Add Work Experience',
            'description' => 'Qualify for better job opportunities abroad',
            'icon' => 'briefcase',
            'priority' => 'medium',
            'route' => 'profile.edit',
        ];
    }
    
    // Personalized service recommendations
    $recommendedServices = [];
    
    // Note: Visa routes removed - using bgproject's tourist-visa system instead
    // Removed student visa and work visa recommendations that referenced non-existent routes
    
    return Inertia::render('Dashboard', [
        'stats' => $stats,
        'profileCompletion' => min($completion, 100),
        'recentActivity' => [],
        'suggestions' => $suggestions,
        'recommendedServices' => $recommendedServices,
        'topReferrers' => $topReferrers,
        'userRank' => $userRank,
        'availableServices' => $activeServices,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // CV Builder routes (must come BEFORE the catch-all /services/{slug} route)
    Route::prefix('services/cv-builder')->name('cv-builder.')->group(function () {
        Route::get('/', [CvBuilderController::class, 'index'])->name('index');
        Route::get('/template/{slug}', [CvBuilderController::class, 'showTemplate'])->name('template');
        Route::get('/create', [CvBuilderController::class, 'create'])->name('create');
        Route::post('/store', [CvBuilderController::class, 'store'])->name('store');
        Route::get('/my-cvs', [CvBuilderController::class, 'myCvs'])->name('my-cvs');
        Route::get('/{id}/edit', [CvBuilderController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CvBuilderController::class, 'update'])->name('update');
        Route::delete('/{id}', [CvBuilderController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/preview', [CvBuilderController::class, 'preview'])->name('preview');
        Route::get('/{id}/download', [CvBuilderController::class, 'download'])->name('download');
    });

    // User Services Routes
    Route::get('/services', [\App\Http\Controllers\ServiceController::class, 'index'])->name('services.index');
    Route::get('/services/{slug}', [\App\Http\Controllers\ServiceController::class, 'show'])->name('services.show');
    
    // Visa Requirements API
    Route::get('/api/visa-requirements', [\App\Http\Controllers\VisaRequirementController::class, 'getRequirements'])->name('visa.requirements');
    
    // User Applications Routes
    Route::post('/my-applications', [\App\Http\Controllers\User\UserApplicationController::class, 'store'])->name('user.applications.store');
    Route::get('/my-applications', [\App\Http\Controllers\User\UserApplicationController::class, 'index'])->name('user.applications.index');
    Route::get('/my-applications/{id}', [\App\Http\Controllers\User\UserApplicationController::class, 'show'])->name('user.applications.show');
    
    // Payment Gateway Routes
    Route::prefix('payment')->name('payment.')->group(function () {
        // Payment history
        Route::get('/', [\App\Http\Controllers\PaymentController::class, 'index'])->name('index');
        Route::get('/{transaction}', [\App\Http\Controllers\PaymentController::class, 'show'])->name('show');
        
        // SSLCommerz routes
        Route::post('/sslcommerz/initiate', [\App\Http\Controllers\PaymentController::class, 'initiateSSLCommerz'])->name('sslcommerz.initiate');
        Route::post('/sslcommerz/success', [\App\Http\Controllers\PaymentController::class, 'sslcommerzSuccess'])->name('sslcommerz.success');
        Route::post('/sslcommerz/fail', [\App\Http\Controllers\PaymentController::class, 'sslcommerzFail'])->name('sslcommerz.fail');
        Route::get('/sslcommerz/cancel', [\App\Http\Controllers\PaymentController::class, 'sslcommerzCancel'])->name('sslcommerz.cancel');
        
        // bKash routes
        Route::post('/bkash/initiate', [\App\Http\Controllers\PaymentController::class, 'initiateBKash'])->name('bkash.initiate');
        Route::get('/bkash/callback', [\App\Http\Controllers\PaymentController::class, 'bkashCallback'])->name('bkash.callback');
        
        // Nagad routes
        Route::post('/nagad/initiate', [\App\Http\Controllers\PaymentController::class, 'initiateNagad'])->name('nagad.initiate');
        Route::get('/nagad/callback', [\App\Http\Controllers\PaymentController::class, 'nagadCallback'])->name('nagad.callback');
    });
});

// Public payment webhook routes (no auth required)
Route::prefix('payment')->name('payment.')->group(function () {
    Route::post('/sslcommerz/ipn', [\App\Http\Controllers\PaymentController::class, 'sslcommerzIPN'])->name('sslcommerz.ipn');
    Route::post('/bkash/webhook', [\App\Http\Controllers\PaymentController::class, 'bkashWebhook'])->name('bkash.webhook');
    Route::post('/nagad/webhook', [\App\Http\Controllers\PaymentController::class, 'nagadWebhook'])->name('nagad.webhook');
});

// Authenticated application quotes routes
Route::middleware('auth')->group(function () {
    Route::get('/my-applications/{id}/quotes', [\App\Http\Controllers\User\UserApplicationController::class, 'quotes'])->name('user.applications.quotes');
    Route::post('/my-applications/{id}/quotes/{quoteId}/accept', [\App\Http\Controllers\User\UserApplicationController::class, 'acceptQuote'])->name('user.applications.quotes.accept');
    Route::post('/my-applications/{id}/quotes/{quoteId}/reject', [\App\Http\Controllers\User\UserApplicationController::class, 'rejectQuote'])->name('user.applications.quotes.reject');
    
    // Onboarding
    Route::get('/onboarding/welcome', [OnboardingController::class, 'welcome'])->name('onboarding.welcome');
    
    // API routes for profile sections
    Route::prefix('api/profile')->name('api.profile.')->group(function () {
        // Family Members
        Route::get('/family-members', [\App\Http\Controllers\Api\Profile\FamilyMemberController::class, 'index'])->name('family.index');
        Route::post('/family-members', [\App\Http\Controllers\Api\Profile\FamilyMemberController::class, 'store'])->name('family.store');
        Route::put('/family-members/{familyMember}', [\App\Http\Controllers\Api\Profile\FamilyMemberController::class, 'update'])->name('family.update');
        Route::delete('/family-members/{familyMember}', [\App\Http\Controllers\Api\Profile\FamilyMemberController::class, 'destroy'])->name('family.destroy');
        
        // Languages
        Route::get('/languages', [\App\Http\Controllers\Api\UserProfile\UserLanguageController::class, 'index'])->name('languages.index');
        Route::post('/languages', [\App\Http\Controllers\Api\UserProfile\UserLanguageController::class, 'store'])->name('languages.store');
        Route::put('/languages/{userLanguage}', [\App\Http\Controllers\Api\UserProfile\UserLanguageController::class, 'update'])->name('languages.update');
        Route::delete('/languages/{userLanguage}', [\App\Http\Controllers\Api\UserProfile\UserLanguageController::class, 'destroy'])->name('languages.destroy');
        
        // Security Information
        Route::get('/security', [\App\Http\Controllers\Api\UserProfile\UserSecurityInformationController::class, 'show'])->name('security.show');
        Route::post('/security', [\App\Http\Controllers\Api\UserProfile\UserSecurityInformationController::class, 'store'])->name('security.store');
        Route::delete('/security', [\App\Http\Controllers\Api\UserProfile\UserSecurityInformationController::class, 'destroy'])->name('security.destroy');

        // Education
        Route::get('/education', [\App\Http\Controllers\Profile\UserEducationController::class, 'index'])->name('education.index');
        Route::post('/education', [\App\Http\Controllers\Profile\UserEducationController::class, 'store'])->name('education.store');
        Route::put('/education/{userEducation}', [\App\Http\Controllers\Profile\UserEducationController::class, 'update'])->name('education.update');
        Route::delete('/education/{userEducation}', [\App\Http\Controllers\Profile\UserEducationController::class, 'destroy'])->name('education.destroy');

        // Work Experience
        Route::get('/work-experience', [\App\Http\Controllers\Profile\UserWorkExperienceController::class, 'index'])->name('work-experience.index');
        Route::post('/work-experience', [\App\Http\Controllers\Profile\UserWorkExperienceController::class, 'store'])->name('work-experience.store');
        Route::put('/work-experience/{userWorkExperience}', [\App\Http\Controllers\Profile\UserWorkExperienceController::class, 'update'])->name('work-experience.update');
        Route::delete('/work-experience/{userWorkExperience}', [\App\Http\Controllers\Profile\UserWorkExperienceController::class, 'destroy'])->name('work-experience.destroy');
        
        // Skills (profile-specific)
        Route::get('/skills', [\App\Http\Controllers\Api\UserSkillController::class, 'index'])->name('skills.index');
        Route::post('/skills', [\App\Http\Controllers\Api\UserSkillController::class, 'store'])->name('skills.store');
        Route::put('/skills/{id}', [\App\Http\Controllers\Api\UserSkillController::class, 'update'])->name('skills.update');
        Route::delete('/skills/{id}', [\App\Http\Controllers\Api\UserSkillController::class, 'destroy'])->name('skills.destroy');
        
        // Phone Numbers
        Route::get('/phone-numbers', [PhoneNumberController::class, 'index'])->name('phone-numbers.index');
        Route::post('/phone-numbers', [PhoneNumberController::class, 'store'])->name('phone-numbers.store');
        Route::put('/phone-numbers/{phoneNumber}', [PhoneNumberController::class, 'update'])->name('phone-numbers.update');
        Route::delete('/phone-numbers/{phoneNumber}', [PhoneNumberController::class, 'destroy'])->name('phone-numbers.destroy');
        Route::post('/phone-numbers/{phoneNumber}/send-verification', [PhoneNumberController::class, 'sendVerificationCode'])->name('phone-numbers.send-verification');
        Route::post('/phone-numbers/{phoneNumber}/verify', [PhoneNumberController::class, 'verifyCode'])->name('phone-numbers.verify');
        Route::post('/phone-numbers/{phoneNumber}/resend-verification', [PhoneNumberController::class, 'resendVerificationCode'])->name('phone-numbers.resend-verification');
    });
    
    // API route for all skills (not profile-specific)
    Route::get('/api/skills', [\App\Http\Controllers\Api\SkillController::class, 'index'])->name('api.skills.index');
    
    // API routes for data management lookups
    Route::get('/api/countries', function () {
        return response()->json(\App\Models\Country::where('is_active', true)->orderBy('name')->get(['id', 'code', 'name', 'name_bn']));
    })->name('api.countries');
    
    Route::get('/api/cities', function (Illuminate\Http\Request $request) {
        $query = \App\Models\City::where('is_active', true)->orderBy('name');
        if ($request->has('country_id')) {
            $query->where('country_id', $request->country_id);
        }
        return response()->json($query->get(['id', 'name', 'name_bn', 'country_id']));
    })->name('api.cities');
    
    // Smart Suggestions
    Route::prefix('suggestions')->name('suggestions.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SmartSuggestionsController::class, 'index'])->name('index');
        Route::post('/{suggestion}/complete', [\App\Http\Controllers\SmartSuggestionsController::class, 'complete'])->name('complete');
        Route::post('/{suggestion}/dismiss', [\App\Http\Controllers\SmartSuggestionsController::class, 'dismiss'])->name('dismiss');
        Route::post('/refresh', [\App\Http\Controllers\SmartSuggestionsController::class, 'refresh'])->name('refresh');
        Route::get('/api', [\App\Http\Controllers\SmartSuggestionsController::class, 'apiIndex'])->name('api');
    });
    
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/details', [ProfileController::class, 'updateDetails'])->name('profile.update.details');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Profile section routes (redirect to profile edit with section)
    Route::get('/profile/financial', function () {
        return redirect()->route('profile.edit', ['section' => 'financial']);
    })->name('profile.financial.index');
    Route::post('/profile/financial', [ProfileController::class, 'updateDetails'])->name('profile.financial.update');
    
    Route::get('/profile/languages', function () {
        return redirect()->route('profile.edit', ['section' => 'languages']);
    })->name('profile.languages.index');

    // Phone Numbers routes
    Route::prefix('phone-numbers')->name('phone-numbers.')->group(function () {
        Route::get('/', [PhoneNumberController::class, 'index'])->name('index');
        Route::post('/', [PhoneNumberController::class, 'store'])->name('store');
        Route::put('/{phoneNumber}', [PhoneNumberController::class, 'update'])->name('update');
        Route::delete('/{phoneNumber}', [PhoneNumberController::class, 'destroy'])->name('destroy');
    });

    // Education routes
    Route::prefix('profile/education')->name('profile.education.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Profile\UserEducationController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Profile\UserEducationController::class, 'store'])->name('store');
        Route::put('/{userEducation}', [\App\Http\Controllers\Profile\UserEducationController::class, 'update'])->name('update');
        Route::delete('/{userEducation}', [\App\Http\Controllers\Profile\UserEducationController::class, 'destroy'])->name('destroy');
    });

    // Work Experience routes
    Route::prefix('profile/work-experience')->name('profile.work-experience.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Profile\UserWorkExperienceController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Profile\UserWorkExperienceController::class, 'store'])->name('store');
        Route::put('/{userWorkExperience}', [\App\Http\Controllers\Profile\UserWorkExperienceController::class, 'update'])->name('update');
        Route::delete('/{userWorkExperience}', [\App\Http\Controllers\Profile\UserWorkExperienceController::class, 'destroy'])->name('destroy');
    });

    // Travel history management routes
    Route::prefix('profile/travel-history')->name('profile.travel-history.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Profile\TravelHistoryController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Profile\TravelHistoryController::class, 'store'])->name('store');
        Route::put('/{id}', [\App\Http\Controllers\Profile\TravelHistoryController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Profile\TravelHistoryController::class, 'destroy'])->name('destroy');
    });

    // Visa history management routes
    Route::prefix('profile/visa-history')->name('profile.visa-history.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Profile\VisaHistoryController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Profile\VisaHistoryController::class, 'store'])->name('store');
        Route::put('/{id}', [\App\Http\Controllers\Profile\VisaHistoryController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Profile\VisaHistoryController::class, 'destroy'])->name('destroy');
    });

    // Passport management routes
    Route::prefix('profile/passports')->name('profile.passports.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Profile\PassportController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Profile\PassportController::class, 'store'])->name('store');
        Route::put('/{id}', [\App\Http\Controllers\Profile\PassportController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Profile\PassportController::class, 'destroy'])->name('destroy');
    });

    // Social links
    Route::post('/profile/social-links', [ProfileController::class, 'updateSocialLinks'])->name('profile.social-links.update');
    
    // Emergency contact
    Route::post('/profile/emergency-contact', [ProfileController::class, 'updateEmergencyContact'])->name('profile.emergency-contact.update');
    
    // Medical information
    Route::post('/profile/medical-info', [ProfileController::class, 'updateMedicalInfo'])->name('profile.medical-info.update');
    
    // References
    Route::post('/profile/references', [ProfileController::class, 'updateReferences'])->name('profile.references.update');
    
    // Certifications
    Route::post('/profile/certifications', [ProfileController::class, 'updateCertifications'])->name('profile.certifications.update');
    
    // Privacy & Data Control
    Route::post('/profile/privacy-settings', [ProfileController::class, 'updatePrivacySettings'])->name('profile.privacy-settings.update');
    Route::get('/profile/download-data', [ProfileController::class, 'downloadData'])->name('profile.download-data');

    // Document Scanner Routes
    Route::prefix('document-scanner')->name('document-scanner.')->group(function () {
        Route::get('/', [\App\Http\Controllers\User\DocumentScanController::class, 'index'])->name('index');
        Route::post('/upload', [\App\Http\Controllers\User\DocumentScanController::class, 'upload'])->name('upload');
        Route::get('/{scan}', [\App\Http\Controllers\User\DocumentScanController::class, 'show'])->name('show');
        Route::post('/{scan}/apply', [\App\Http\Controllers\User\DocumentScanController::class, 'applyToProfile'])->name('apply');
        Route::post('/{scan}/reprocess', [\App\Http\Controllers\User\DocumentScanController::class, 'reprocess'])->name('reprocess');
        Route::delete('/{scan}', [\App\Http\Controllers\User\DocumentScanController::class, 'destroy'])->name('destroy');
    });
    
    // Preferences
    Route::post('/profile/preferences', [ProfileController::class, 'updatePreferences'])->name('profile.preferences.update');

    // Wallet routes
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::get('/wallet/transactions', [WalletController::class, 'transactions'])->name('wallet.transactions');
    Route::post('/wallet/add-funds', [WalletController::class, 'addFunds'])->name('wallet.add-funds');
    Route::post('/wallet/withdraw', [WalletController::class, 'withdraw'])->name('wallet.withdraw');

    // Referral routes
    Route::get('/referrals', [ReferralController::class, 'index'])->name('referral.index');
    Route::get('/referrals/list', [ReferralController::class, 'referrals'])->name('referral.referrals');
    Route::get('/referrals/rewards', [ReferralController::class, 'rewards'])->name('referral.rewards');

    // FAQ routes
    Route::prefix('faqs')->name('faqs.')->group(function () {
        Route::get('/', [\App\Http\Controllers\User\FaqController::class, 'index'])->name('index');
        Route::get('/{faq}', [\App\Http\Controllers\User\FaqController::class, 'show'])->name('show');
    });

    // Events routes
    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/', [\App\Http\Controllers\User\EventController::class, 'index'])->name('index');
        Route::get('/{event}', [\App\Http\Controllers\User\EventController::class, 'show'])->name('show');
        Route::post('/{event}/register', [\App\Http\Controllers\User\EventController::class, 'register'])->name('register');
        Route::delete('/{event}/cancel', [\App\Http\Controllers\User\EventController::class, 'cancelRegistration'])->name('cancel');
        Route::get('/my-events', [\App\Http\Controllers\User\EventController::class, 'myEvents'])->name('my-events');
    });

    // Support Ticket routes
    Route::prefix('support')->name('support.')->group(function () {
        Route::get('/', [\App\Http\Controllers\User\SupportTicketController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\User\SupportTicketController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\User\SupportTicketController::class, 'store'])->name('store');
        Route::get('/{ticket}', [\App\Http\Controllers\User\SupportTicketController::class, 'show'])->name('show');
        Route::post('/{ticket}/reply', [\App\Http\Controllers\User\SupportTicketController::class, 'reply'])->name('reply');
        Route::post('/{ticket}/close', [\App\Http\Controllers\User\SupportTicketController::class, 'close'])->name('close');
    });

    // Appointment routes
    Route::prefix('appointments')->name('appointments.')->group(function () {
        Route::get('/', [\App\Http\Controllers\User\AppointmentController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\User\AppointmentController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\User\AppointmentController::class, 'store'])->name('store');
        Route::get('/{appointment}', [\App\Http\Controllers\User\AppointmentController::class, 'show'])->name('show');
        Route::put('/{appointment}', [\App\Http\Controllers\User\AppointmentController::class, 'update'])->name('update');
        Route::delete('/{appointment}', [\App\Http\Controllers\User\AppointmentController::class, 'destroy'])->name('destroy');
    });

    // Document Scanner routes
    Route::prefix('document-scanner')->name('document-scanner.')->group(function () {
        Route::get('/', [\App\Http\Controllers\User\DocumentScanController::class, 'index'])->name('index');
        Route::post('/scan', [\App\Http\Controllers\User\DocumentScanController::class, 'scan'])->name('scan');
        Route::get('/{scan}/download', [\App\Http\Controllers\User\DocumentScanController::class, 'download'])->name('download');
    });

    // Suggestions route (dynamic smart suggestions)
    Route::get('/suggestions', function () {
        return Inertia::render('User/Suggestions', [
            'suggestions' => auth()->user()->getSmartSuggestions()
        ]);
    })->name('suggestions');

    // Profile Assessment routes
    Route::prefix('profile/assessment')->name('profile.assessment.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ProfileAssessmentController::class, 'show'])->name('show');
        Route::post('/generate', [\App\Http\Controllers\ProfileAssessmentController::class, 'generate'])->name('generate');
        Route::get('/recommendations', [\App\Http\Controllers\ProfileAssessmentController::class, 'recommendations'])->name('recommendations');
        Route::get('/score-breakdown', [\App\Http\Controllers\ProfileAssessmentController::class, 'scoreBreakdown'])->name('score-breakdown');
        Route::get('/visa-eligibility', [\App\Http\Controllers\ProfileAssessmentController::class, 'visaEligibility'])->name('visa-eligibility');
    });
    
    // Alias for profile-assessment (with hyphen)
    Route::get('/profile-assessment', [\App\Http\Controllers\ProfileAssessmentController::class, 'show'])->name('profile-assessment.show');

    // Public Profile Settings routes
    Route::prefix('profile/public')->name('profile.public.')->group(function () {
        Route::get('/settings', [\App\Http\Controllers\PublicProfileController::class, 'settings'])->name('settings');
        Route::post('/settings', [\App\Http\Controllers\PublicProfileController::class, 'updateSettings'])->name('update-settings');
        Route::get('/qr-code', [\App\Http\Controllers\PublicProfileController::class, 'generateQrCode'])->name('qr-code');
    });

    // Travel Insurance routes
    Route::prefix('services/travel-insurance')->name('travel-insurance.')->group(function () {
        Route::get('/', [TravelInsuranceController::class, 'index'])->name('index');
        Route::get('/{slug}', [TravelInsuranceController::class, 'show'])->name('show');
        Route::get('/{slug}/book', [TravelInsuranceController::class, 'bookingForm'])->name('booking-form');
        Route::post('/book', [TravelInsuranceController::class, 'book'])->name('book');
        Route::get('/my-bookings', [TravelInsuranceController::class, 'myBookings'])->name('my-bookings');
        Route::get('/booking/{id}', [TravelInsuranceController::class, 'bookingDetails'])->name('booking-details');
    });

    // Tourist Visa Application routes
    Route::prefix('profile/tourist-visa')->name('profile.tourist-visa.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Profile\TouristVisaController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Profile\TouristVisaController::class, 'create'])->name('create');
        Route::get('/requirements/{country}', [\App\Http\Controllers\Profile\TouristVisaController::class, 'getRequirements'])->name('requirements');
        Route::get('/{touristVisa}', [\App\Http\Controllers\Profile\TouristVisaController::class, 'show'])->name('show');
    });

    // Student Visa Application routes (NEW!)
    Route::prefix('profile/student-visa')->name('profile.student-visa.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Profile\StudentVisaController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Profile\StudentVisaController::class, 'create'])->name('create');
        Route::get('/requirements/{country}', [\App\Http\Controllers\Profile\StudentVisaController::class, 'getRequirements'])->name('requirements');
        Route::get('/{studentVisa}', [\App\Http\Controllers\Profile\StudentVisaController::class, 'show'])->name('show');
    });

    // Work Visa Application routes (NEW!)
    Route::prefix('profile/work-visa')->name('profile.work-visa.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Profile\WorkVisaController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Profile\WorkVisaController::class, 'create'])->name('create');
        Route::get('/requirements/{country}', [\App\Http\Controllers\Profile\WorkVisaController::class, 'getRequirements'])->name('requirements');
        Route::get('/{workVisa}', [\App\Http\Controllers\Profile\WorkVisaController::class, 'show'])->name('show');
    });

    // Translation Service routes (NEW!)
    Route::prefix('profile/translation')->name('profile.translation.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Profile\TranslationController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Profile\TranslationController::class, 'create'])->name('create');
        Route::get('/{translation}', [\App\Http\Controllers\Profile\TranslationController::class, 'show'])->name('show');
    });

    // Attestation Service routes (NEW!)
    Route::prefix('profile/attestation')->name('profile.attestation.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Profile\AttestationController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Profile\AttestationController::class, 'create'])->name('create');
        Route::get('/{attestation}', [\App\Http\Controllers\Profile\AttestationController::class, 'show'])->name('show');
    });

    // Hajj & Umrah Service routes (NEW!)
    Route::prefix('profile/hajj-umrah')->name('profile.hajj-umrah.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Profile\HajjUmrahController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Profile\HajjUmrahController::class, 'create'])->name('create');
        Route::get('/{hajjUmrah}', [\App\Http\Controllers\Profile\HajjUmrahController::class, 'show'])->name('show');
    });

    // Service Quote routes (for users to view and accept quotes)
    Route::prefix('profile/service-applications')->name('profile.service-applications.')->group(function () {
        Route::get('/{application}/quotes', [\App\Http\Controllers\Api\Profile\ServiceQuoteController::class, 'index'])->name('quotes.index');
        Route::get('/{application}/quotes/compare', [\App\Http\Controllers\Api\Profile\ServiceQuoteController::class, 'compare'])->name('quotes.compare');
    });

    Route::prefix('profile/service-quotes')->name('profile.service-quotes.')->group(function () {
        Route::post('/{quote}/accept', [\App\Http\Controllers\Api\Profile\ServiceQuoteController::class, 'accept'])->name('accept');
        Route::post('/{quote}/reject', [\App\Http\Controllers\Api\Profile\ServiceQuoteController::class, 'reject'])->name('reject');
    });

    // Flight Booking routes
    Route::prefix('services/flight-booking')->name('flight-booking.')->group(function () {
        Route::get('/', [FlightBookingController::class, 'index'])->name('index');
        Route::post('/search', [FlightBookingController::class, 'search'])->name('search');
        Route::get('/{routeId}/book', [FlightBookingController::class, 'bookingForm'])->name('booking-form');
        Route::post('/book', [FlightBookingController::class, 'book'])->name('book');
        Route::get('/my-bookings', [FlightBookingController::class, 'myBookings'])->name('my-bookings');
        Route::get('/booking/{id}', [FlightBookingController::class, 'bookingDetails'])->name('booking-details');
        Route::post('/booking/{id}/cancel', [FlightBookingController::class, 'cancel'])->name('cancel');
        Route::get('/booking/{id}/ticket', [FlightBookingController::class, 'downloadTicket'])->name('download-ticket');
    });

    // Flight Request routes (Request-based system)
    Route::prefix('services/flight-requests')->name('flight-requests.')->group(function () {
        Route::get('/create', [FlightRequestController::class, 'create'])->name('create');
        Route::post('/', [FlightRequestController::class, 'store'])->name('store');
        Route::get('/', [FlightRequestController::class, 'index'])->name('index');
        Route::get('/{id}', [FlightRequestController::class, 'show'])->name('show');
        Route::post('/{requestId}/quotes/{quoteId}/accept', [FlightRequestController::class, 'acceptQuote'])->name('accept-quote');
        Route::post('/{id}/cancel', [FlightRequestController::class, 'cancel'])->name('cancel');
        Route::post('/{requestId}/quotes/{quoteId}/accept', [FlightRequestController::class, 'acceptQuote'])->name('accept-quote');
        Route::post('/{id}/cancel', [FlightRequestController::class, 'cancel'])->name('cancel');
    });

    // Hotel Booking routes
    Route::prefix('services/hotels')->name('hotels.')->group(function () {
        Route::get('/', [HotelBookingController::class, 'index'])->name('index');
        Route::get('/{hotel}', [HotelBookingController::class, 'show'])->name('show');
        Route::get('/{hotel}/rooms/{room}/book', [HotelBookingController::class, 'create'])->name('book');
        Route::post('/bookings', [HotelBookingController::class, 'store'])->name('bookings.store');
        Route::get('/bookings/my-bookings', [HotelBookingController::class, 'myBookings'])->name('my-bookings');
        Route::get('/bookings/{booking}', [HotelBookingController::class, 'showBooking'])->name('bookings.show');
        Route::get('/bookings/{booking}/payment', [HotelBookingController::class, 'payment'])->name('bookings.payment');
        Route::post('/bookings/{booking}/payment', [HotelBookingController::class, 'processPayment'])->name('bookings.process-payment');
        Route::get('/bookings/{booking}/confirmation', [HotelBookingController::class, 'confirmation'])->name('bookings.confirmation');
        Route::post('/bookings/{booking}/cancel', [HotelBookingController::class, 'cancel'])->name('bookings.cancel');
    });

    // Note: Generic visa application routes removed - use bgproject's dedicated tourist-visa system instead

    // Translation Service routes
    Route::prefix('services/translation')->name('translation.')->group(function () {
        Route::get('/', [\App\Http\Controllers\TranslationRequestController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\TranslationRequestController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\TranslationRequestController::class, 'store'])->name('store');
        Route::get('/my-requests', [\App\Http\Controllers\TranslationRequestController::class, 'myRequests'])->name('my-requests');
        Route::get('/{translation}', [\App\Http\Controllers\TranslationRequestController::class, 'show'])->name('show');
        Route::post('/{translation}/cancel', [\App\Http\Controllers\TranslationRequestController::class, 'cancel'])->name('cancel');
    });
});

// Additional authenticated routes (documents, jobs, notifications)
Route::middleware('auth')->group(function () {
    // User Documents (upload & listing)
    Route::get('/documents', [\App\Http\Controllers\DocumentController::class, 'index'])->name('documents.index');
    Route::post('/documents', [\App\Http\Controllers\DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{document}/download', [\App\Http\Controllers\DocumentController::class, 'download'])->name('documents.download');
    Route::delete('/documents/{document}', [\App\Http\Controllers\DocumentController::class, 'destroy'])->name('documents.destroy');

    // Job Posting routes
    Route::prefix('jobs')->name('jobs.')->group(function () {
        Route::get('/', [JobController::class, 'index'])->name('index');
        Route::get('/{id}', [JobController::class, 'show'])->name('show');
        Route::post('/{id}/apply', [JobController::class, 'apply'])->name('apply');
        Route::get('/my/applications', [JobController::class, 'myApplications'])->name('my-applications');
    });

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/dropdown', [\App\Http\Controllers\NotificationController::class, 'dropdown'])->name('notifications.dropdown');
    Route::get('/notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::delete('/notifications/{notification}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Notification Preferences
    Route::get('/notification-preferences', [\App\Http\Controllers\User\NotificationPreferenceController::class, 'index'])->name('notification-preferences.index');
    Route::post('/notification-preferences', [\App\Http\Controllers\User\NotificationPreferenceController::class, 'update'])->name('notification-preferences.update');
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Analytics Dashboard
    Route::get('/analytics', [\App\Http\Controllers\Admin\AnalyticsController::class, 'dashboard'])->name('analytics.dashboard');
    
    // Service Management Dashboard
    Route::get('/services', [\App\Http\Controllers\Admin\ServiceManagementController::class, 'index'])->name('services.index');
    
    // Data Management
    Route::prefix('data')->name('data.')->group(function () {
        // Countries Management
        Route::resource('countries', \App\Http\Controllers\Admin\DataManagement\CountryController::class);
        Route::post('/countries/{country}/toggle-status', [\App\Http\Controllers\Admin\DataManagement\CountryController::class, 'toggleStatus'])->name('countries.toggle-status');
        Route::get('/countries-bulk-upload', [\App\Http\Controllers\Admin\DataManagement\CountryController::class, 'bulkUpload'])->name('countries.bulk-upload');
        Route::post('/countries-process-upload', [\App\Http\Controllers\Admin\DataManagement\CountryController::class, 'processBulkUpload'])->name('countries.process-upload');
        Route::get('/countries-template', [\App\Http\Controllers\Admin\DataManagement\CountryController::class, 'downloadTemplate'])->name('countries.template');
        Route::get('/countries-export', [\App\Http\Controllers\Admin\DataManagement\CountryController::class, 'export'])->name('countries.export');
        
        // Currencies Management
        Route::resource('currencies', \App\Http\Controllers\Admin\DataManagement\CurrencyController::class);
        Route::post('/currencies/{currency}/toggle-status', [\App\Http\Controllers\Admin\DataManagement\CurrencyController::class, 'toggleStatus'])->name('currencies.toggle-status');
        Route::get('/currencies-bulk-upload', [\App\Http\Controllers\Admin\DataManagement\CurrencyController::class, 'bulkUpload'])->name('currencies.bulk-upload');
        Route::post('/currencies-process-upload', [\App\Http\Controllers\Admin\DataManagement\CurrencyController::class, 'processBulkUpload'])->name('currencies.process-upload');
        Route::get('/currencies-template', [\App\Http\Controllers\Admin\DataManagement\CurrencyController::class, 'downloadTemplate'])->name('currencies.template');
        Route::get('/currencies-export', [\App\Http\Controllers\Admin\DataManagement\CurrencyController::class, 'export'])->name('currencies.export');
        
        // Languages Management
        Route::resource('languages', \App\Http\Controllers\Admin\DataManagement\LanguageController::class);
        Route::post('/languages/{language}/toggle-status', [\App\Http\Controllers\Admin\DataManagement\LanguageController::class, 'toggleStatus'])->name('languages.toggle-status');
        Route::get('/languages-bulk-upload', [\App\Http\Controllers\Admin\DataManagement\LanguageController::class, 'bulkUpload'])->name('languages.bulk-upload');
        Route::post('/languages-process-upload', [\App\Http\Controllers\Admin\DataManagement\LanguageController::class, 'processBulkUpload'])->name('languages.process-upload');
        Route::get('/languages-template', [\App\Http\Controllers\Admin\DataManagement\LanguageController::class, 'downloadTemplate'])->name('languages.template');
        Route::get('/languages-export', [\App\Http\Controllers\Admin\DataManagement\LanguageController::class, 'export'])->name('languages.export');
        
        // Language Tests Management
        Route::resource('language-tests', \App\Http\Controllers\Admin\DataManagement\LanguageTestController::class);
        Route::post('/language-tests/{languageTest}/toggle-status', [\App\Http\Controllers\Admin\DataManagement\LanguageTestController::class, 'toggleStatus'])->name('language-tests.toggle-status');
        Route::get('/language-tests-bulk-upload', [\App\Http\Controllers\Admin\DataManagement\LanguageTestController::class, 'bulkUpload'])->name('language-tests.bulk-upload');
        Route::post('/language-tests-process-upload', [\App\Http\Controllers\Admin\DataManagement\LanguageTestController::class, 'processBulkUpload'])->name('language-tests.process-upload');
        Route::get('/language-tests-template', [\App\Http\Controllers\Admin\DataManagement\LanguageTestController::class, 'downloadTemplate'])->name('language-tests.template');
        Route::get('/language-tests-export', [\App\Http\Controllers\Admin\DataManagement\LanguageTestController::class, 'export'])->name('language-tests.export');
        
        // Job Categories Management
        Route::resource('job-categories', \App\Http\Controllers\Admin\DataManagement\JobCategoryController::class);
        Route::post('/job-categories/{jobCategory}/toggle-status', [\App\Http\Controllers\Admin\DataManagement\JobCategoryController::class, 'toggleStatus'])->name('job-categories.toggle-status');
        Route::get('/job-categories-bulk-upload', [\App\Http\Controllers\Admin\DataManagement\JobCategoryController::class, 'bulkUpload'])->name('job-categories.bulk-upload');
        Route::post('/job-categories-process-upload', [\App\Http\Controllers\Admin\DataManagement\JobCategoryController::class, 'processBulkUpload'])->name('job-categories.process-upload');
        Route::get('/job-categories-template', [\App\Http\Controllers\Admin\DataManagement\JobCategoryController::class, 'downloadTemplate'])->name('job-categories.template');
        Route::get('/job-categories-export', [\App\Http\Controllers\Admin\DataManagement\JobCategoryController::class, 'export'])->name('job-categories.export');
        
        // Skill Categories Management
        Route::resource('skill-categories', \App\Http\Controllers\Admin\DataManagement\SkillCategoryController::class);
        Route::post('/skill-categories/{skillCategory}/toggle-status', [\App\Http\Controllers\Admin\DataManagement\SkillCategoryController::class, 'toggleStatus'])->name('skill-categories.toggle-status');
        Route::get('/skill-categories-bulk-upload', [\App\Http\Controllers\Admin\DataManagement\SkillCategoryController::class, 'bulkUpload'])->name('skill-categories.bulk-upload');
        Route::post('/skill-categories-process-upload', [\App\Http\Controllers\Admin\DataManagement\SkillCategoryController::class, 'processBulkUpload'])->name('skill-categories.process-upload');
        Route::get('/skill-categories-template', [\App\Http\Controllers\Admin\DataManagement\SkillCategoryController::class, 'downloadTemplate'])->name('skill-categories.template');
        Route::get('/skill-categories-export', [\App\Http\Controllers\Admin\DataManagement\SkillCategoryController::class, 'export'])->name('skill-categories.export');
        
        // Skills Management
        Route::resource('skills', \App\Http\Controllers\Admin\DataManagement\SkillController::class);
        Route::post('/skills/{skill}/toggle-status', [\App\Http\Controllers\Admin\DataManagement\SkillController::class, 'toggleStatus'])->name('skills.toggle-status');
        Route::get('/skills-bulk-upload', [\App\Http\Controllers\Admin\DataManagement\SkillController::class, 'bulkUpload'])->name('skills.bulk-upload');
        Route::post('/skills-process-upload', [\App\Http\Controllers\Admin\DataManagement\SkillController::class, 'processBulkUpload'])->name('skills.process-upload');
        Route::get('/skills-template', [\App\Http\Controllers\Admin\DataManagement\SkillController::class, 'downloadTemplate'])->name('skills.template');
        Route::get('/skills-export', [\App\Http\Controllers\Admin\DataManagement\SkillController::class, 'export'])->name('skills.export');
        
        // Cities Management
        Route::resource('cities', \App\Http\Controllers\Admin\DataManagement\CityController::class);
        Route::post('/cities/{city}/toggle-status', [\App\Http\Controllers\Admin\DataManagement\CityController::class, 'toggleStatus'])->name('cities.toggle-status');
        Route::get('/cities-bulk-upload', [\App\Http\Controllers\Admin\DataManagement\CityController::class, 'bulkUpload'])->name('cities-bulk-upload');
        Route::post('/cities-process-upload', [\App\Http\Controllers\Admin\DataManagement\CityController::class, 'processBulkUpload'])->name('cities-process-upload');
        Route::get('/cities-template', [\App\Http\Controllers\Admin\DataManagement\CityController::class, 'downloadTemplate'])->name('cities-template');
        Route::get('/cities-export', [\App\Http\Controllers\Admin\DataManagement\CityController::class, 'export'])->name('cities-export');
        
        // Airports Management
        Route::resource('airports', \App\Http\Controllers\API\AirportController::class);
        Route::post('/airports/{airport}/toggle-status', [\App\Http\Controllers\API\AirportController::class, 'toggleStatus'])->name('airports.toggle-status');
        Route::get('/airports-bulk-upload', [\App\Http\Controllers\API\AirportController::class, 'bulkUpload'])->name('airports.bulk-upload');
        Route::post('/airports-process-upload', [\App\Http\Controllers\API\AirportController::class, 'processBulkUpload'])->name('airports.process-upload');
        Route::get('/airports-template', [\App\Http\Controllers\API\AirportController::class, 'downloadTemplate'])->name('airports.template');
        Route::get('/airports-export', [\App\Http\Controllers\API\AirportController::class, 'export'])->name('airports.export');
        
        // Degrees Management
        Route::resource('degrees', \App\Http\Controllers\Admin\DataManagement\DegreeController::class);
        Route::post('/degrees/{degree}/toggle-status', [\App\Http\Controllers\Admin\DataManagement\DegreeController::class, 'toggleStatus'])->name('degrees.toggle-status');
        Route::get('/degrees-bulk-upload', [\App\Http\Controllers\Admin\DataManagement\DegreeController::class, 'bulkUpload'])->name('degrees.bulk-upload');
        Route::post('/degrees-process-upload', [\App\Http\Controllers\Admin\DataManagement\DegreeController::class, 'processBulkUpload'])->name('degrees.process-upload');
        Route::get('/degrees-template', [\App\Http\Controllers\Admin\DataManagement\DegreeController::class, 'downloadTemplate'])->name('degrees.template');
        Route::get('/degrees-export', [\App\Http\Controllers\Admin\DataManagement\DegreeController::class, 'export'])->name('degrees.export');
        
        // Service Categories Management
        Route::resource('service-categories', \App\Http\Controllers\Admin\DataManagement\ServiceCategoryController::class);
        Route::post('/service-categories/{serviceCategory}/toggle-status', [\App\Http\Controllers\Admin\DataManagement\ServiceCategoryController::class, 'toggleStatus'])->name('service-categories.toggle-status');
        Route::get('/service-categories-bulk-upload', [\App\Http\Controllers\Admin\DataManagement\ServiceCategoryController::class, 'bulkUpload'])->name('service-categories.bulk-upload');
        Route::post('/service-categories-process-upload', [\App\Http\Controllers\Admin\DataManagement\ServiceCategoryController::class, 'processBulkUpload'])->name('service-categories.process-upload');
        Route::get('/service-categories-template', [\App\Http\Controllers\Admin\DataManagement\ServiceCategoryController::class, 'downloadTemplate'])->name('service-categories.template');
        Route::get('/service-categories-export', [\App\Http\Controllers\Admin\DataManagement\ServiceCategoryController::class, 'export'])->name('service-categories.export');
        
        // Blog Categories Management
        Route::resource('blog-categories', \App\Http\Controllers\Admin\DataManagement\BlogCategoryController::class);
        Route::post('/blog-categories/{blogCategory}/toggle-status', [\App\Http\Controllers\Admin\DataManagement\BlogCategoryController::class, 'toggleStatus'])->name('blog-categories.toggle-status');
        Route::get('/blog-categories-bulk-upload', [\App\Http\Controllers\Admin\DataManagement\BlogCategoryController::class, 'bulkUpload'])->name('blog-categories.bulk-upload');
        Route::post('/blog-categories-process-upload', [\App\Http\Controllers\Admin\DataManagement\BlogCategoryController::class, 'processBulkUpload'])->name('blog-categories.process-upload');
        Route::get('/blog-categories-template', [\App\Http\Controllers\Admin\DataManagement\BlogCategoryController::class, 'downloadTemplate'])->name('blog-categories.template');
        Route::get('/blog-categories-export', [\App\Http\Controllers\Admin\DataManagement\BlogCategoryController::class, 'export'])->name('blog-categories.export');
        
        // Blog Tags Management
        Route::resource('blog-tags', \App\Http\Controllers\Admin\DataManagement\BlogTagController::class);
        Route::get('/blog-tags-bulk-upload', [\App\Http\Controllers\Admin\DataManagement\BlogTagController::class, 'bulkUpload'])->name('blog-tags.bulk-upload');
        Route::post('/blog-tags-process-upload', [\App\Http\Controllers\Admin\DataManagement\BlogTagController::class, 'processBulkUpload'])->name('blog-tags.process-upload');
        Route::get('/blog-tags-template', [\App\Http\Controllers\Admin\DataManagement\BlogTagController::class, 'downloadTemplate'])->name('blog-tags.template');
        Route::get('/blog-tags-export', [\App\Http\Controllers\Admin\DataManagement\BlogTagController::class, 'export'])->name('blog-tags.export');

        // Email Templates
        Route::post('email-templates/{emailTemplate}/toggle-status', [\App\Http\Controllers\Admin\DataManagement\EmailTemplateController::class, 'toggleStatus'])->name('email-templates.toggle-status');
        Route::resource('email-templates', \App\Http\Controllers\Admin\DataManagement\EmailTemplateController::class);
        Route::get('/email-templates-bulk-upload', [\App\Http\Controllers\Admin\DataManagement\EmailTemplateController::class, 'bulkUpload'])->name('email-templates.bulk-upload');
        Route::post('/email-templates-process-upload', [\App\Http\Controllers\Admin\DataManagement\EmailTemplateController::class, 'processBulkUpload'])->name('email-templates.process-upload');
        Route::get('/email-templates-template', [\App\Http\Controllers\Admin\DataManagement\EmailTemplateController::class, 'downloadTemplate'])->name('email-templates.template');
        Route::get('/email-templates-export', [\App\Http\Controllers\Admin\DataManagement\EmailTemplateController::class, 'export'])->name('email-templates.export');

        // CV Templates Management
        Route::post('cv-templates/{cvTemplate}/toggle-status', [\App\Http\Controllers\Admin\DataManagement\CvTemplateController::class, 'toggleStatus'])->name('cv-templates.toggle-status');
        Route::resource('cv-templates', \App\Http\Controllers\Admin\DataManagement\CvTemplateController::class);
        Route::get('/cv-templates-bulk-upload', [\App\Http\Controllers\Admin\DataManagement\CvTemplateController::class, 'bulkUpload'])->name('cv-templates.bulk-upload');
        Route::post('/cv-templates-process-upload', [\App\Http\Controllers\Admin\DataManagement\CvTemplateController::class, 'processBulkUpload'])->name('cv-templates.process-upload');
        Route::get('/cv-templates-template', [\App\Http\Controllers\Admin\DataManagement\CvTemplateController::class, 'downloadTemplate'])->name('cv-templates.template');
        Route::get('/cv-templates-export', [\App\Http\Controllers\Admin\DataManagement\CvTemplateController::class, 'export'])->name('cv-templates.export');

        // SEO Settings Management
        Route::resource('seo-settings', \App\Http\Controllers\Admin\DataManagement\SeoSettingController::class);
        Route::get('/seo-settings-bulk-upload', [\App\Http\Controllers\Admin\DataManagement\SeoSettingController::class, 'bulkUpload'])->name('seo-settings.bulk-upload');
        Route::post('/seo-settings-process-upload', [\App\Http\Controllers\Admin\DataManagement\SeoSettingController::class, 'processBulkUpload'])->name('seo-settings.process-upload');
        Route::get('/seo-settings-template', [\App\Http\Controllers\Admin\DataManagement\SeoSettingController::class, 'downloadTemplate'])->name('seo-settings.template');
        Route::get('/seo-settings-export', [\App\Http\Controllers\Admin\DataManagement\SeoSettingController::class, 'export'])->name('seo-settings.export');

        // Smart Suggestions Management
        Route::resource('smart-suggestions', \App\Http\Controllers\Admin\DataManagement\SmartSuggestionController::class);
        Route::post('/smart-suggestions/{smartSuggestion}/mark-completed', [\App\Http\Controllers\Admin\DataManagement\SmartSuggestionController::class, 'markCompleted'])->name('smart-suggestions.mark-completed');
        Route::post('/smart-suggestions/{smartSuggestion}/dismiss', [\App\Http\Controllers\Admin\DataManagement\SmartSuggestionController::class, 'dismiss'])->name('smart-suggestions.dismiss');
        Route::get('/smart-suggestions-bulk-upload', [\App\Http\Controllers\Admin\DataManagement\SmartSuggestionController::class, 'bulkUpload'])->name('smart-suggestions.bulk-upload');
        Route::post('/smart-suggestions-process-upload', [\App\Http\Controllers\Admin\DataManagement\SmartSuggestionController::class, 'processBulkUpload'])->name('smart-suggestions.process-upload');
        Route::get('/smart-suggestions-template', [\App\Http\Controllers\Admin\DataManagement\SmartSuggestionController::class, 'downloadTemplate'])->name('smart-suggestions.template');
        Route::get('/smart-suggestions-export', [\App\Http\Controllers\Admin\DataManagement\SmartSuggestionController::class, 'export'])->name('smart-suggestions.export');

        // System Events Management
        Route::resource('system-events', \App\Http\Controllers\Admin\DataManagement\SystemEventController::class);
        Route::get('/system-events-bulk-upload', [\App\Http\Controllers\Admin\DataManagement\SystemEventController::class, 'bulkUpload'])->name('system-events.bulk-upload');
        Route::post('/system-events-process-upload', [\App\Http\Controllers\Admin\DataManagement\SystemEventController::class, 'processBulkUpload'])->name('system-events.process-upload');
        Route::get('/system-events-template', [\App\Http\Controllers\Admin\DataManagement\SystemEventController::class, 'downloadTemplate'])->name('system-events.template');
        Route::get('/system-events-export', [\App\Http\Controllers\Admin\DataManagement\SystemEventController::class, 'export'])->name('system-events.export');

        // Visa Types Management
        Route::resource('visa-types', \App\Http\Controllers\Admin\DataManagement\VisaTypeController::class);
        Route::post('/visa-types/{visaType}/toggle-status', [\App\Http\Controllers\Admin\DataManagement\VisaTypeController::class, 'toggleStatus'])->name('visa-types.toggle-status');
        Route::get('/visa-types-bulk-upload', [\App\Http\Controllers\Admin\DataManagement\VisaTypeController::class, 'bulkUpload'])->name('visa-types.bulk-upload');
        Route::post('/visa-types-process-upload', [\App\Http\Controllers\Admin\DataManagement\VisaTypeController::class, 'processBulkUpload'])->name('visa-types.process-upload');
        Route::get('/visa-types-template', [\App\Http\Controllers\Admin\DataManagement\VisaTypeController::class, 'downloadTemplate'])->name('visa-types.template');
        Route::get('/visa-types-export', [\App\Http\Controllers\Admin\DataManagement\VisaTypeController::class, 'export'])->name('visa-types.export');

        // Document Types Management
        Route::resource('document-types', \App\Http\Controllers\Admin\DataManagement\DocumentTypeController::class);
        Route::post('/document-types/{documentType}/toggle-status', [\App\Http\Controllers\Admin\DataManagement\DocumentTypeController::class, 'toggleStatus'])->name('document-types.toggle-status');
        Route::get('/document-types-bulk-upload', [\App\Http\Controllers\Admin\DataManagement\DocumentTypeController::class, 'bulkUpload'])->name('document-types.bulk-upload');
        Route::post('/document-types-process-upload', [\App\Http\Controllers\Admin\DataManagement\DocumentTypeController::class, 'processBulkUpload'])->name('document-types.process-upload');
        Route::get('/document-types-template', [\App\Http\Controllers\Admin\DataManagement\DocumentTypeController::class, 'downloadTemplate'])->name('document-types.template');
        Route::get('/document-types-export', [\App\Http\Controllers\Admin\DataManagement\DocumentTypeController::class, 'export'])->name('document-types.export');

        // Relationship Types Management
        Route::resource('relationship-types', \App\Http\Controllers\Admin\DataManagement\RelationshipTypeController::class);
        Route::post('/relationship-types/{relationshipType}/toggle-status', [\App\Http\Controllers\Admin\DataManagement\RelationshipTypeController::class, 'toggleStatus'])->name('relationship-types.toggle-status');
        Route::get('/relationship-types-bulk-upload', [\App\Http\Controllers\Admin\DataManagement\RelationshipTypeController::class, 'bulkUpload'])->name('relationship-types.bulk-upload');
        Route::post('/relationship-types-process-upload', [\App\Http\Controllers\Admin\DataManagement\RelationshipTypeController::class, 'processBulkUpload'])->name('relationship-types.process-upload');
        Route::get('/relationship-types-template', [\App\Http\Controllers\Admin\DataManagement\RelationshipTypeController::class, 'downloadTemplate'])->name('relationship-types.template');
        Route::get('/relationship-types-export', [\App\Http\Controllers\Admin\DataManagement\RelationshipTypeController::class, 'export'])->name('relationship-types.export');

        // Bank Names Management
        Route::resource('bank-names', \App\Http\Controllers\Admin\DataManagement\BankNameController::class);
        Route::post('/bank-names/{bankName}/toggle-status', [\App\Http\Controllers\Admin\DataManagement\BankNameController::class, 'toggleStatus'])->name('bank-names.toggle-status');
        Route::get('/bank-names-bulk-upload', [\App\Http\Controllers\Admin\DataManagement\BankNameController::class, 'bulkUpload'])->name('bank-names.bulk-upload');
        Route::post('/bank-names-process-upload', [\App\Http\Controllers\Admin\DataManagement\BankNameController::class, 'processBulkUpload'])->name('bank-names.process-upload');
        Route::get('/bank-names-template', [\App\Http\Controllers\Admin\DataManagement\BankNameController::class, 'downloadTemplate'])->name('bank-names.template');
        Route::get('/bank-names-export', [\App\Http\Controllers\Admin\DataManagement\BankNameController::class, 'export'])->name('bank-names.export');

        // Institution Types Management
        Route::resource('institution-types', \App\Http\Controllers\Admin\DataManagement\InstitutionTypeController::class);
        Route::post('/institution-types/{institutionType}/toggle-status', [\App\Http\Controllers\Admin\DataManagement\InstitutionTypeController::class, 'toggleStatus'])->name('institution-types.toggle-status');
        Route::get('/institution-types-bulk-upload', [\App\Http\Controllers\Admin\DataManagement\InstitutionTypeController::class, 'bulkUpload'])->name('institution-types.bulk-upload');
        Route::post('/institution-types-process-upload', [\App\Http\Controllers\Admin\DataManagement\InstitutionTypeController::class, 'processBulkUpload'])->name('institution-types.process-upload');
        Route::get('/institution-types-template', [\App\Http\Controllers\Admin\DataManagement\InstitutionTypeController::class, 'downloadTemplate'])->name('institution-types.template');
        Route::get('/institution-types-export', [\App\Http\Controllers\Admin\DataManagement\InstitutionTypeController::class, 'export'])->name('institution-types.export');
    });
    
    Route::get('/wallets', [\App\Http\Controllers\Admin\WalletController::class, 'index'])->name('wallets.index');
    Route::get('/wallets/{wallet}', [\App\Http\Controllers\Admin\WalletController::class, 'show'])->name('wallets.show');
    Route::post('/wallets/{wallet}/credit', [\App\Http\Controllers\Admin\WalletController::class, 'credit'])->name('wallets.credit');
    Route::post('/wallets/{wallet}/debit', [\App\Http\Controllers\Admin\WalletController::class, 'debit'])->name('wallets.debit');
    Route::post('/wallets/{wallet}/toggle-status', [\App\Http\Controllers\Admin\WalletController::class, 'toggleStatus'])->name('wallets.toggle-status');
    Route::post('/transactions/{transaction}/reverse', [\App\Http\Controllers\Admin\WalletController::class, 'reverseTransaction'])->name('transactions.reverse');

    // Rewards management
    Route::get('/rewards', [\App\Http\Controllers\Admin\RewardController::class, 'index'])->name('rewards.index');
    Route::post('/rewards/{reward}/approve', [\App\Http\Controllers\Admin\RewardController::class, 'approve'])->name('rewards.approve');
    Route::post('/rewards/{reward}/reject', [\App\Http\Controllers\Admin\RewardController::class, 'reject'])->name('rewards.reject');

    // Flight Requests management
    Route::prefix('flight-requests')->name('flight-requests.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\FlightRequestController::class, 'index'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\Admin\FlightRequestController::class, 'show'])->name('show');
        Route::post('/{id}/assign', [\App\Http\Controllers\Admin\FlightRequestController::class, 'assign'])->name('assign');
        Route::post('/{id}/notes', [\App\Http\Controllers\Admin\FlightRequestController::class, 'updateNotes'])->name('update-notes');
        Route::post('/{id}/cancel', [\App\Http\Controllers\Admin\FlightRequestController::class, 'cancel'])->name('cancel');
        Route::post('/bulk-assign', [\App\Http\Controllers\Admin\FlightRequestController::class, 'bulkAssign'])->name('bulk-assign');
    });

    // Hotel Management
    Route::prefix('hotels')->name('hotels.')->group(function () {
        Route::get('/', [AdminHotelController::class, 'index'])->name('index');
        Route::get('/create', [AdminHotelController::class, 'create'])->name('create');
        Route::post('/', [AdminHotelController::class, 'store'])->name('store');
        Route::get('/{hotel}', [AdminHotelController::class, 'show'])->name('show');
        Route::get('/{hotel}/edit', [AdminHotelController::class, 'edit'])->name('edit');
        Route::put('/{hotel}', [AdminHotelController::class, 'update'])->name('update');
        Route::delete('/{hotel}', [AdminHotelController::class, 'destroy'])->name('destroy');
        Route::post('/{hotel}/toggle-status', [AdminHotelController::class, 'toggleStatus'])->name('toggle-status');
        
        // Hotel Rooms
        Route::get('/{hotel}/rooms', [AdminHotelController::class, 'rooms'])->name('rooms');
        Route::post('/{hotel}/rooms', [AdminHotelController::class, 'storeRoom'])->name('rooms.store');
        Route::put('/{hotel}/rooms/{room}', [AdminHotelController::class, 'updateRoom'])->name('rooms.update');
        Route::delete('/{hotel}/rooms/{room}', [AdminHotelController::class, 'destroyRoom'])->name('rooms.destroy');
    });

    // Hotel Bookings Management
    Route::prefix('hotel-bookings')->name('hotel-bookings.')->group(function () {
        Route::get('/', [AdminHotelController::class, 'bookings'])->name('index');
        Route::get('/{booking}', [AdminHotelController::class, 'showBooking'])->name('show');
        Route::post('/{booking}/status', [AdminHotelController::class, 'updateBookingStatus'])->name('update-status');
    });

    // Hotel Analytics
    Route::get('/hotels-analytics', [AdminHotelController::class, 'analytics'])->name('hotels.analytics');

    // Visa Applications Management
    Route::prefix('visa-applications')->name('visa-applications.')->group(function () {
        Route::get('/', [AdminVisaController::class, 'index'])->name('index');
        Route::get('/{application}', [AdminVisaController::class, 'show'])->name('show');
        Route::post('/{application}/status', [AdminVisaController::class, 'updateStatus'])->name('update-status');
        Route::post('/{application}/assign', [AdminVisaController::class, 'assign'])->name('assign');
        Route::post('/{application}/approve', [AdminVisaController::class, 'approve'])->name('approve');
        Route::post('/{application}/reject', [AdminVisaController::class, 'reject'])->name('reject');
        Route::post('/{application}/request-documents', [AdminVisaController::class, 'requestDocuments'])->name('request-documents');
        Route::post('/{application}/schedule-appointment', [AdminVisaController::class, 'scheduleAppointment'])->name('schedule-appointment');
        Route::post('/{application}/priority', [AdminVisaController::class, 'updatePriority'])->name('update-priority');
        Route::post('/{application}/notes', [AdminVisaController::class, 'addNote'])->name('add-note');
        Route::post('/documents/{document}/verify', [AdminVisaController::class, 'verifyDocument'])->name('verify-document');
    });

    // Admin Document Verification
    Route::get('/documents/verify', [\App\Http\Controllers\Admin\AdminDocumentVerificationController::class, 'index'])->name('documents.verify.index');
    Route::post('/documents/{document}/approve', [\App\Http\Controllers\Admin\AdminDocumentVerificationController::class, 'approve'])->name('documents.approve');
    Route::post('/documents/{document}/reject', [\App\Http\Controllers\Admin\AdminDocumentVerificationController::class, 'reject'])->name('documents.reject');

    // Admin Notifications
    Route::get('/notifications', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/broadcast', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'store'])->name('notifications.broadcast');
});

// Agency routes
Route::middleware(['auth', 'role:agency'])->prefix('agency')->name('agency.')->group(function () {
    // Visa Management - Agency manages their assigned countries
    Route::prefix('visa-management')->name('visa-management.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Agency\VisaManagementController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Agency\VisaManagementController::class, 'statistics'])->name('statistics');
        Route::get('/{country}/{visaType}', [\App\Http\Controllers\Agency\VisaManagementController::class, 'show'])->name('show');
        
        // Update visa requirement fees
        Route::put('/visa-requirements/{visaRequirement}/fees', [\App\Http\Controllers\Agency\VisaManagementController::class, 'updateFees'])->name('update-fees');
        Route::put('/visa-requirements/{visaRequirement}', [\App\Http\Controllers\Agency\VisaManagementController::class, 'updateRequirement'])->name('update-requirement');
        
        // Document management
        Route::post('/visa-requirements/{visaRequirement}/documents', [\App\Http\Controllers\Agency\VisaManagementController::class, 'storeDocument'])->name('store-document');
        Route::put('/documents/{document}', [\App\Http\Controllers\Agency\VisaManagementController::class, 'updateDocument'])->name('update-document');
        Route::delete('/documents/{document}', [\App\Http\Controllers\Agency\VisaManagementController::class, 'destroyDocument'])->name('destroy-document');
        
        // Profession requirements management
        Route::post('/visa-requirements/{visaRequirement}/professions', [\App\Http\Controllers\Agency\VisaManagementController::class, 'storeProfession'])->name('store-profession');
        Route::put('/professions/{profession}', [\App\Http\Controllers\Agency\VisaManagementController::class, 'updateProfession'])->name('update-profession');
        Route::delete('/professions/{profession}', [\App\Http\Controllers\Agency\VisaManagementController::class, 'destroyProfession'])->name('destroy-profession');
    });
    
    // Flight requests
    Route::prefix('flight-requests')->name('flight-requests.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Agency\FlightRequestController::class, 'index'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\Agency\FlightRequestController::class, 'show'])->name('show');
        Route::get('/{id}/quote/create', [\App\Http\Controllers\Agency\FlightRequestController::class, 'createQuote'])->name('create-quote');
        Route::post('/{id}/quote', [\App\Http\Controllers\Agency\FlightRequestController::class, 'storeQuote'])->name('store-quote');
        Route::put('/{id}/quote/{quoteId}', [\App\Http\Controllers\Agency\FlightRequestController::class, 'updateQuote'])->name('update-quote');
    });
});


require __DIR__.'/auth.php';

// OAuth Routes (public)
Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

// Blog routes (public)
Route::get('/blog', [\App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [\App\Http\Controllers\BlogController::class, 'show'])->name('blog.show');

// Admin blog routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('blog-posts', \App\Http\Controllers\Admin\BlogPostController::class)->names([
        'index' => 'blog.posts.index',
        'create' => 'blog.posts.create',
        'store' => 'blog.posts.store',
        'show' => 'blog.posts.show',
        'edit' => 'blog.posts.edit',
        'update' => 'blog.posts.update',
        'destroy' => 'blog.posts.destroy',
    ])->parameters(['blog-posts' => 'post']);
    
    Route::resource('blog-categories', \App\Http\Controllers\Admin\BlogCategoryController::class)->names([
        'index' => 'blog.categories.index',
        'store' => 'blog.categories.store',
        'update' => 'blog.categories.update',
        'destroy' => 'blog.categories.destroy',
    ])->except(['show', 'create', 'edit'])->parameters(['blog-categories' => 'category']);
    
    Route::resource('blog-tags', \App\Http\Controllers\Admin\BlogTagController::class)->names([
        'index' => 'blog.tags.index',
        'store' => 'blog.tags.store',
        'update' => 'blog.tags.update',
        'destroy' => 'blog.tags.destroy',
    ])->except(['show', 'create', 'edit'])->parameters(['blog-tags' => 'tag']);
});

// Admin Routes
Route::prefix('admin')->middleware(['auth'])->group(function () {
    // Redirect /admin to /admin/dashboard
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    })->name('admin');
    
    Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('admin.dashboard');
    
    // Translation Demo - Shows how multi-language system works
    Route::get('/translation-demo', function () {
        return inertia('Admin/TranslationDemo');
    })->name('admin.translation-demo');
    
    // Admin Sitemap - Test all admin links
    Route::get('/sitemap', [\App\Http\Controllers\Admin\AdminSitemapController::class, 'index'])->name('admin.sitemap');
    
    // Service Modules Management
    Route::prefix('service-modules')->name('admin.service-modules.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ServiceModuleController::class, 'index'])->name('index');
        Route::get('/{serviceModule}', [\App\Http\Controllers\Admin\ServiceModuleController::class, 'show'])->name('show');
        Route::put('/{serviceModule}', [\App\Http\Controllers\Admin\ServiceModuleController::class, 'update'])->name('update');
        Route::post('/{serviceModule}/toggle-active', [\App\Http\Controllers\Admin\ServiceModuleController::class, 'toggleActive'])->name('toggle-active');
        Route::post('/{serviceModule}/toggle-featured', [\App\Http\Controllers\Admin\ServiceModuleController::class, 'toggleFeatured'])->name('toggle-featured');
        Route::post('/{serviceModule}/toggle-coming-soon', [\App\Http\Controllers\Admin\ServiceModuleController::class, 'toggleComingSoon'])->name('toggle-coming-soon');
        Route::post('/bulk-update', [\App\Http\Controllers\Admin\ServiceModuleController::class, 'bulkUpdate'])->name('bulk-update');
        Route::get('/{serviceModule}/analytics', [\App\Http\Controllers\Admin\ServiceModuleController::class, 'analytics'])->name('analytics');
    });
    
    // Plugin System - Service Applications Management
    Route::prefix('service-applications')->name('admin.service-applications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ServiceApplicationController::class, 'index'])->name('index');
        Route::get('/{serviceApplication}', [\App\Http\Controllers\Admin\ServiceApplicationController::class, 'show'])->name('show');
        Route::put('/{serviceApplication}/status', [\App\Http\Controllers\Admin\ServiceApplicationController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{serviceApplication}', [\App\Http\Controllers\Admin\ServiceApplicationController::class, 'destroy'])->name('destroy');
        Route::get('/export', [\App\Http\Controllers\Admin\ServiceApplicationController::class, 'export'])->name('export');
    });
    
    // Plugin System - Service Quotes Management
    Route::prefix('service-quotes')->name('admin.service-quotes.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ServiceQuoteController::class, 'index'])->name('index');
        Route::get('/{serviceQuote}', [\App\Http\Controllers\Admin\ServiceQuoteController::class, 'show'])->name('show');
        Route::put('/{serviceQuote}/status', [\App\Http\Controllers\Admin\ServiceQuoteController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{serviceQuote}', [\App\Http\Controllers\Admin\ServiceQuoteController::class, 'destroy'])->name('destroy');
    });
    
    // Visa Requirements Management
    Route::prefix('visa-requirements')->name('admin.visa-requirements.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\VisaRequirementController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\VisaRequirementController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\VisaRequirementController::class, 'store'])->name('store');
        Route::get('/{visaRequirement}', [\App\Http\Controllers\Admin\VisaRequirementController::class, 'show'])->name('show');
        Route::get('/{visaRequirement}/edit', [\App\Http\Controllers\Admin\VisaRequirementController::class, 'edit'])->name('edit');
        Route::put('/{visaRequirement}', [\App\Http\Controllers\Admin\VisaRequirementController::class, 'update'])->name('update');
        Route::delete('/{visaRequirement}', [\App\Http\Controllers\Admin\VisaRequirementController::class, 'destroy'])->name('destroy');
        Route::post('/{visaRequirement}/toggle-active', [\App\Http\Controllers\Admin\VisaRequirementController::class, 'toggleActive'])->name('toggle-active');
        Route::post('/{visaRequirement}/documents', [\App\Http\Controllers\Admin\VisaRequirementController::class, 'addDocument'])->name('add-document');
        Route::post('/{visaRequirement}/profession-requirements', [\App\Http\Controllers\Admin\VisaRequirementController::class, 'addProfessionRequirement'])->name('add-profession');
    });

    // Document Hub System
    Route::resource('document-categories', \App\Http\Controllers\Admin\DocumentCategoryController::class)->names('admin.document-categories');
    Route::resource('master-documents', \App\Http\Controllers\Admin\MasterDocumentController::class)->names('admin.master-documents');
    
    Route::prefix('document-assignments')->name('admin.document-assignments.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\CountryDocumentAssignmentController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\CountryDocumentAssignmentController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\CountryDocumentAssignmentController::class, 'store'])->name('store');
        Route::get('/{country}', [\App\Http\Controllers\Admin\CountryDocumentAssignmentController::class, 'show'])->name('show');
        Route::delete('/{assignment}', [\App\Http\Controllers\Admin\CountryDocumentAssignmentController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-assign', [\App\Http\Controllers\Admin\CountryDocumentAssignmentController::class, 'bulkAssign'])->name('bulk-assign');
    });
    
    // Agency Country Assignments Management
    Route::prefix('agency-assignments')->name('admin.agency-assignments.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AgencyAssignmentController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\AgencyAssignmentController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\AgencyAssignmentController::class, 'store'])->name('store');
        Route::get('/{agencyAssignment}', [\App\Http\Controllers\Admin\AgencyAssignmentController::class, 'show'])->name('show');
        Route::put('/{agencyAssignment}', [\App\Http\Controllers\Admin\AgencyAssignmentController::class, 'update'])->name('update');
        Route::delete('/{agencyAssignment}', [\App\Http\Controllers\Admin\AgencyAssignmentController::class, 'destroy'])->name('destroy');
        Route::post('/{agencyAssignment}/toggle-active', [\App\Http\Controllers\Admin\AgencyAssignmentController::class, 'toggleActive'])->name('toggle-active');
        Route::post('/assign-requirement', [\App\Http\Controllers\Admin\AgencyAssignmentController::class, 'assignRequirement'])->name('assign-requirement');
        Route::post('/visa-requirements/{visaRequirement}/unassign', [\App\Http\Controllers\Admin\AgencyAssignmentController::class, 'unassignRequirement'])->name('unassign-requirement');
        Route::post('/visa-requirements/{visaRequirement}/update-commission', [\App\Http\Controllers\Admin\AgencyAssignmentController::class, 'updateCommission'])->name('update-commission');
    });
    
    // Agency Resources Management (Exclusive Resource Model)
    Route::prefix('agency-resources')->name('admin.agency-resources.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AgencyResourceController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\AgencyResourceController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\AgencyResourceController::class, 'store'])->name('store');
        Route::get('/{agencyResource}', [\App\Http\Controllers\Admin\AgencyResourceController::class, 'show'])->name('show');
        Route::get('/{agencyResource}/edit', [\App\Http\Controllers\Admin\AgencyResourceController::class, 'edit'])->name('edit');
        Route::put('/{agencyResource}', [\App\Http\Controllers\Admin\AgencyResourceController::class, 'update'])->name('update');
        Route::delete('/{agencyResource}', [\App\Http\Controllers\Admin\AgencyResourceController::class, 'destroy'])->name('destroy');
        Route::post('/{agencyResource}/approve', [\App\Http\Controllers\Admin\AgencyResourceController::class, 'approve'])->name('approve');
        Route::post('/{agencyResource}/reject', [\App\Http\Controllers\Admin\AgencyResourceController::class, 'reject'])->name('reject');
        Route::post('/check-availability', [\App\Http\Controllers\Admin\AgencyResourceController::class, 'checkAvailability'])->name('check-availability');
    });
    
    // Job Postings Management
    Route::resource('jobs', AdminJobPostingController::class)->names([
        'index' => 'admin.jobs.index',
        'create' => 'admin.jobs.create',
        'store' => 'admin.jobs.store',
        'show' => 'admin.jobs.show',
        'edit' => 'admin.jobs.edit',
        'update' => 'admin.jobs.update',
        'destroy' => 'admin.jobs.destroy',
    ]);
    Route::post('/jobs/{id}/toggle-featured', [AdminJobPostingController::class, 'toggleFeatured'])->name('admin.jobs.toggle-featured');
    Route::post('/jobs/{id}/toggle-active', [AdminJobPostingController::class, 'toggleActive'])->name('admin.jobs.toggle-active');
    Route::post('/jobs/{id}/approve', [AdminJobPostingController::class, 'approve'])->name('admin.jobs.approve');
    Route::post('/jobs/{id}/reject', [AdminJobPostingController::class, 'reject'])->name('admin.jobs.reject');
    Route::post('/jobs/bulk-delete', [AdminJobPostingController::class, 'bulkDelete'])->name('admin.jobs.bulk-delete');
    Route::post('/jobs/bulk-update-status', [AdminJobPostingController::class, 'bulkUpdateStatus'])->name('admin.jobs.bulk-update-status');
    
    // Job Applications Management
    Route::get('/applications', [AdminJobApplicationController::class, 'index'])->name('admin.applications.index');
    Route::get('/job-applications', [AdminJobApplicationController::class, 'index'])->name('admin.job-applications.index'); // Alias
    Route::get('/applications/{id}', [AdminJobApplicationController::class, 'show'])->name('admin.applications.show');
    Route::post('/applications/{id}/update-status', [AdminJobApplicationController::class, 'updateStatus'])->name('admin.applications.update-status');
    Route::post('/applications/bulk-update-status', [AdminJobApplicationController::class, 'bulkUpdateStatus'])->name('admin.applications.bulk-update-status');
    Route::get('/applications/export', [AdminJobApplicationController::class, 'export'])->name('admin.applications.export');
    
    // FAQ Management
    Route::resource('faqs', \App\Http\Controllers\Admin\FaqController::class)->names('admin.faqs');
    Route::post('/faqs/reorder', [\App\Http\Controllers\Admin\FaqController::class, 'reorder'])->name('admin.faqs.reorder');
    
    // FAQ Categories Management
    Route::resource('faq-categories', \App\Http\Controllers\Admin\FaqCategoryController::class)->names('admin.faq-categories');
    Route::post('/faq-categories/reorder', [\App\Http\Controllers\Admin\FaqCategoryController::class, 'reorder'])->name('admin.faq-categories.reorder');
    
    // Events Management
    Route::resource('events', \App\Http\Controllers\Admin\EventController::class)->names('admin.events');
    Route::post('/events/{event}/toggle-featured', [\App\Http\Controllers\Admin\EventController::class, 'toggleFeatured'])->name('admin.events.toggle-featured');
    Route::post('/events/{event}/toggle-published', [\App\Http\Controllers\Admin\EventController::class, 'togglePublished'])->name('admin.events.toggle-published');
    
    // CMS Pages Management
    Route::resource('pages', \App\Http\Controllers\Admin\PageController::class)->names('admin.pages');
    Route::post('/pages/{page}/toggle-published', [\App\Http\Controllers\Admin\PageController::class, 'togglePublished'])->name('admin.pages.toggle-published');
    Route::post('/pages/{page}/duplicate', [\App\Http\Controllers\Admin\PageController::class, 'duplicate'])->name('admin.pages.duplicate');
    
    // Site Settings Management
    Route::get('/settings', [\App\Http\Controllers\Admin\SiteSettingController::class, 'index'])->name('admin.settings.index');
    Route::post('/settings', [\App\Http\Controllers\Admin\SiteSettingController::class, 'update'])->name('admin.settings.update');
    Route::post('/settings/update-single', [\App\Http\Controllers\Admin\SiteSettingController::class, 'updateSingle'])->name('admin.settings.update-single');
    Route::delete('/settings/file', [\App\Http\Controllers\Admin\SiteSettingController::class, 'deleteFile'])->name('admin.settings.delete-file');
    Route::post('/settings/reset', [\App\Http\Controllers\Admin\SiteSettingController::class, 'reset'])->name('admin.settings.reset');
    Route::post('/settings/clear-cache', [\App\Http\Controllers\Admin\SiteSettingController::class, 'clearCache'])->name('admin.settings.clear-cache');
    
    // Support Tickets Management
    Route::resource('support-tickets', \App\Http\Controllers\Admin\SupportTicketController::class)->names('admin.support-tickets');
    Route::post('/support-tickets/{supportTicket}/assign', [\App\Http\Controllers\Admin\SupportTicketController::class, 'assign'])->name('admin.support-tickets.assign');
    Route::post('/support-tickets/{supportTicket}/close', [\App\Http\Controllers\Admin\SupportTicketController::class, 'close'])->name('admin.support-tickets.close');
    Route::post('/support-tickets/{supportTicket}/reopen', [\App\Http\Controllers\Admin\SupportTicketController::class, 'reopen'])->name('admin.support-tickets.reopen');
    Route::post('/support-tickets/{supportTicket}/reply', [\App\Http\Controllers\Admin\SupportTicketController::class, 'reply'])->name('admin.support-tickets.reply');
    Route::post('/support-tickets/{supportTicket}/update-priority', [\App\Http\Controllers\Admin\SupportTicketController::class, 'updatePriority'])->name('admin.support-tickets.update-priority');
    
    // Appointments Management
    Route::resource('appointments', \App\Http\Controllers\Admin\AppointmentController::class)->names('admin.appointments');
    Route::post('/appointments/{appointment}/confirm', [\App\Http\Controllers\Admin\AppointmentController::class, 'confirm'])->name('admin.appointments.confirm');
    Route::post('/appointments/{appointment}/cancel', [\App\Http\Controllers\Admin\AppointmentController::class, 'cancel'])->name('admin.appointments.cancel');
    Route::post('/appointments/{appointment}/reschedule', [\App\Http\Controllers\Admin\AppointmentController::class, 'reschedule'])->name('admin.appointments.reschedule');
    Route::post('/appointments/{appointment}/complete', [\App\Http\Controllers\Admin\AppointmentController::class, 'complete'])->name('admin.appointments.complete');
    Route::get('/appointments/calendar', [\App\Http\Controllers\Admin\AppointmentController::class, 'calendar'])->name('admin.appointments.calendar');
    
    // Marketing Campaigns Management
    Route::resource('marketing-campaigns', \App\Http\Controllers\Admin\MarketingCampaignController::class)->names('admin.marketing-campaigns');
    Route::post('/marketing-campaigns/{marketingCampaign}/activate', [\App\Http\Controllers\Admin\MarketingCampaignController::class, 'activate'])->name('admin.marketing-campaigns.activate');
    Route::post('/marketing-campaigns/{marketingCampaign}/pause', [\App\Http\Controllers\Admin\MarketingCampaignController::class, 'pause'])->name('admin.marketing-campaigns.pause');
    Route::post('/marketing-campaigns/{marketingCampaign}/complete', [\App\Http\Controllers\Admin\MarketingCampaignController::class, 'complete'])->name('admin.marketing-campaigns.complete');
    Route::post('/marketing-campaigns/{marketingCampaign}/duplicate', [\App\Http\Controllers\Admin\MarketingCampaignController::class, 'duplicate'])->name('admin.marketing-campaigns.duplicate');
    Route::get('/marketing-campaigns/{marketingCampaign}/analytics', [\App\Http\Controllers\Admin\MarketingCampaignController::class, 'analytics'])->name('admin.marketing-campaigns.analytics');
    
    // Partners Management
    Route::resource('partners', \App\Http\Controllers\Admin\PartnerController::class)->names('admin.partners');
    Route::post('/partners/{partner}/toggle-active', [\App\Http\Controllers\Admin\PartnerController::class, 'toggleActive'])->name('admin.partners.toggle-active');
    Route::post('/partners/update-order', [\App\Http\Controllers\Admin\PartnerController::class, 'updateOrder'])->name('admin.partners.update-order');
    
    // Testimonials Management
    Route::resource('testimonials', \App\Http\Controllers\Admin\TestimonialController::class)->names('admin.testimonials');
    Route::post('/testimonials/{testimonial}/toggle-approved', [\App\Http\Controllers\Admin\TestimonialController::class, 'toggleApproved'])->name('admin.testimonials.toggle-approved');
    Route::post('/testimonials/{testimonial}/toggle-featured', [\App\Http\Controllers\Admin\TestimonialController::class, 'toggleFeatured'])->name('admin.testimonials.toggle-featured');
    Route::post('/testimonials/{testimonial}/toggle-active', [\App\Http\Controllers\Admin\TestimonialController::class, 'toggleActive'])->name('admin.testimonials.toggle-active');
    Route::post('/testimonials/update-order', [\App\Http\Controllers\Admin\TestimonialController::class, 'updateOrder'])->name('admin.testimonials.update-order');
    
    // Directory Categories Management
    Route::resource('directory-categories', \App\Http\Controllers\Admin\DirectoryCategoryController::class)->names('admin.directory-categories');
    Route::post('/directory-categories/{directory_category}/toggle-active', [\App\Http\Controllers\Admin\DirectoryCategoryController::class, 'toggleActive'])->name('admin.directory-categories.toggle-active');
    Route::post('/directory-categories/update-order', [\App\Http\Controllers\Admin\DirectoryCategoryController::class, 'updateOrder'])->name('admin.directory-categories.update-order');
    
    // Directories Management
    Route::resource('directories', \App\Http\Controllers\Admin\DirectoryController::class)->names('admin.directories');
    Route::post('/directories/{directory}/toggle-verified', [\App\Http\Controllers\Admin\DirectoryController::class, 'toggleVerified'])->name('admin.directories.toggle-verified');
    Route::post('/directories/{directory}/toggle-featured', [\App\Http\Controllers\Admin\DirectoryController::class, 'toggleFeatured'])->name('admin.directories.toggle-featured');
    Route::post('/directories/{directory}/toggle-published', [\App\Http\Controllers\Admin\DirectoryController::class, 'togglePublished'])->name('admin.directories.toggle-published');
    
    // User Management
    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{id}', [AdminUserController::class, 'show'])->name('admin.users.show');
    Route::get('/users/{id}/edit', [AdminUserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{id}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::post('/users/{id}/suspend', [AdminUserController::class, 'suspend'])->name('admin.users.suspend');
    Route::post('/users/{id}/unsuspend', [AdminUserController::class, 'unsuspend'])->name('admin.users.unsuspend');
    Route::post('/users/{id}/update-role', [AdminUserController::class, 'updateRole'])->name('admin.users.update-role');
    // Impersonation (admin only, will be guarded within controller by role check)
    Route::post('/users/{id}/impersonate', [\App\Http\Controllers\Admin\AdminImpersonationController::class, 'impersonate'])
        ->name('admin.users.impersonate');
    Route::post('/impersonation/leave', [\App\Http\Controllers\Admin\AdminImpersonationController::class, 'leave'])->name('admin.impersonation.leave');
    // Admin Impersonation Logs (Audit)
    Route::get('/impersonations', [\App\Http\Controllers\Admin\AdminImpersonationLogController::class, 'index'])->name('admin.impersonations.index');
    Route::get('/impersonations/export', [\App\Http\Controllers\Admin\AdminImpersonationLogController::class, 'export'])->name('admin.impersonations.export');
    
    // Agency Verification
    Route::prefix('agency-verification')->name('agency-verification.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AgencyVerificationController::class, 'index'])->name('index');
        Route::get('/dashboard', [\App\Http\Controllers\Admin\AgencyVerificationController::class, 'dashboard'])->name('dashboard');
        Route::get('/{verificationRequest}', [\App\Http\Controllers\Admin\AgencyVerificationController::class, 'show'])->name('show');
        Route::post('/{verificationRequest}/update-status', [\App\Http\Controllers\Admin\AgencyVerificationController::class, 'updateStatus'])->name('update-status');
        Route::post('/documents/{document}/update-status', [\App\Http\Controllers\Admin\AgencyVerificationController::class, 'updateDocumentStatus'])->name('update-document-status');
    });
    
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
    Route::post('/users/bulk-suspend', [AdminUserController::class, 'bulkSuspend'])->name('admin.users.bulk-suspend');
    Route::post('/users/bulk-unsuspend', [AdminUserController::class, 'bulkUnsuspend'])->name('admin.users.bulk-unsuspend');
    Route::get('/users/export', [AdminUserController::class, 'export'])->name('admin.users.export');
    
    // Analytics & Reports
    Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('admin.analytics.index');
    Route::get('/analytics/export', [AdminAnalyticsController::class, 'export'])->name('admin.analytics.export');
    
    // Settings Management
    Route::get('/settings', [AdminSettingsController::class, 'index'])->name('admin.settings.index');
    Route::post('/settings', [AdminSettingsController::class, 'update'])->name('admin.settings.update');
    Route::post('/settings/seed', [AdminSettingsController::class, 'seed'])->name('admin.settings.seed');
    
    // SEO Settings Management
    Route::prefix('seo-settings')->name('seo-settings.')->middleware('role:admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SeoSettingsController::class, 'index'])->name('index');
        Route::put('/{pageType}', [\App\Http\Controllers\Admin\SeoSettingsController::class, 'update'])->name('update');
        Route::delete('/{pageType}', [\App\Http\Controllers\Admin\SeoSettingsController::class, 'destroy'])->name('destroy');
        Route::post('/generate-sitemap', [\App\Http\Controllers\Admin\SeoSettingsController::class, 'generateSitemap'])->name('generate-sitemap');
    });
    
    // Marketing Campaigns
    Route::prefix('marketing-campaigns')->name('marketing-campaigns.')->group(function () {
        Route::post('{campaign}/pause', [\App\Http\Controllers\Admin\MarketingCampaignController::class, 'pause'])->name('pause');
        Route::post('{campaign}/resume', [\App\Http\Controllers\Admin\MarketingCampaignController::class, 'resume'])->name('resume');
        Route::post('{campaign}/cancel', [\App\Http\Controllers\Admin\MarketingCampaignController::class, 'cancel'])->name('cancel');
        Route::post('{campaign}/duplicate', [\App\Http\Controllers\Admin\MarketingCampaignController::class, 'duplicate'])->name('duplicate');
    });
    Route::resource('marketing-campaigns', \App\Http\Controllers\Admin\MarketingCampaignController::class)->names('admin.marketing-campaigns');
    
    // Service Applications Management
    Route::prefix('service-applications')->name('service-applications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ServiceApplicationController::class, 'index'])->name('index');
        Route::get('/{application}', [\App\Http\Controllers\Admin\ServiceApplicationController::class, 'show'])->name('show');
        Route::post('/{application}/assign-agency', [\App\Http\Controllers\Admin\ServiceApplicationController::class, 'assignAgency'])->name('assign-agency');
        Route::post('/{application}/update-status', [\App\Http\Controllers\Admin\ServiceApplicationController::class, 'updateStatus'])->name('update-status');
    });
});

// Public profile route (no auth required) - placed AFTER authenticated routes to avoid conflicts
// Exclude reserved path segments like 'assessment', 'edit', 'financial', 'languages' used by authenticated routes
Route::get('/profile/{slug}', [\App\Http\Controllers\PublicProfileController::class, 'show'])
    ->where('slug', '^(?!assessment|edit|financial|languages|education|work-experience|passports|travel-history|visa-history).*')
    ->name('profile.public.show');

// Agency Routes (for agency users)
Route::middleware(['auth', 'verified'])->prefix('agency')->name('agency.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Agency\DashboardController::class, 'index'])->name('dashboard');
    
    // Earnings & Analytics
    Route::get('/earnings', [\App\Http\Controllers\Agency\EarningsController::class, 'index'])->name('earnings.index');
    
    // Country Assignments
    Route::get('/country-assignments', [\App\Http\Controllers\Agency\CountryAssignmentController::class, 'index'])->name('country-assignments.index');
    
    // Applications
    Route::get('/applications', [\App\Http\Controllers\Agency\ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [\App\Http\Controllers\Agency\ApplicationController::class, 'show'])->name('applications.show');
    Route::post('/applications/{application}/update-status', [\App\Http\Controllers\Agency\ApplicationController::class, 'updateStatus'])->name('applications.update-status');
    
    // Quotes
    Route::get('/applications/{application}/quote/create', [\App\Http\Controllers\Agency\QuoteController::class, 'create'])->name('quotes.create');
    Route::post('/applications/{application}/quote', [\App\Http\Controllers\Agency\QuoteController::class, 'store'])->name('quotes.store');
    Route::get('/quotes/{quote}/edit', [\App\Http\Controllers\Agency\QuoteController::class, 'edit'])->name('quotes.edit');
    Route::put('/quotes/{quote}', [\App\Http\Controllers\Agency\QuoteController::class, 'update'])->name('quotes.update');
    
    // Agency Profile
    Route::get('/profile', [\App\Http\Controllers\Agency\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [\App\Http\Controllers\Agency\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\Agency\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/logo', [\App\Http\Controllers\Agency\ProfileController::class, 'uploadLogo'])->name('profile.logo');
    Route::post('/profile/office-images', [\App\Http\Controllers\Agency\ProfileController::class, 'uploadOfficeImages'])->name('profile.office-images');
    Route::delete('/profile/office-images', [\App\Http\Controllers\Agency\ProfileController::class, 'deleteOfficeImage'])->name('profile.delete-office-image');
    
    // Team Members
    Route::get('/team', [\App\Http\Controllers\Agency\TeamMemberController::class, 'index'])->name('team.index');
    Route::get('/team/create', [\App\Http\Controllers\Agency\TeamMemberController::class, 'create'])->name('team.create');
    Route::post('/team', [\App\Http\Controllers\Agency\TeamMemberController::class, 'store'])->name('team.store');
    Route::get('/team/{teamMember}/edit', [\App\Http\Controllers\Agency\TeamMemberController::class, 'edit'])->name('team.edit');
    Route::put('/team/{teamMember}', [\App\Http\Controllers\Agency\TeamMemberController::class, 'update'])->name('team.update');
    Route::post('/team/{teamMember}/photo', [\App\Http\Controllers\Agency\TeamMemberController::class, 'uploadPhoto'])->name('team.photo');
    Route::delete('/team/{teamMember}', [\App\Http\Controllers\Agency\TeamMemberController::class, 'destroy'])->name('team.destroy');
    Route::post('/team/reorder', [\App\Http\Controllers\Agency\TeamMemberController::class, 'reorder'])->name('team.reorder');
    
    // Consultant Invitations
    Route::get('/team/invite', [\App\Http\Controllers\Agency\ConsultantInvitationController::class, 'create'])->name('team.invite');
    Route::post('/team/invite', [\App\Http\Controllers\Agency\ConsultantInvitationController::class, 'store'])->name('team.invite.store');
    
    // Verification
    Route::get('/verification', [\App\Http\Controllers\Agency\VerificationController::class, 'index'])->name('verification.index');
    Route::post('/verification/documents', [\App\Http\Controllers\Agency\VerificationController::class, 'uploadDocument'])->name('verification.upload-document');
    Route::delete('/verification/documents/{document}', [\App\Http\Controllers\Agency\VerificationController::class, 'deleteDocument'])->name('verification.delete-document');
    Route::post('/verification/submit', [\App\Http\Controllers\Agency\VerificationController::class, 'submitRequest'])->name('verification.submit');
});

// Consultant Routes (for consultant users)
Route::middleware(['auth', 'verified'])->prefix('consultant')->name('consultant.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Consultant\DashboardController::class, 'index'])->name('dashboard');
});

// Consultant Invitation Routes (public - no auth)
Route::prefix('consultant')->name('consultant.')->group(function () {
    Route::get('/accept-invitation/{token}', [\App\Http\Controllers\Agency\ConsultantInvitationController::class, 'acceptInvitation'])->name('accept-invitation');
    Route::post('/complete-invitation/{token}', [\App\Http\Controllers\Agency\ConsultantInvitationController::class, 'completeInvitation'])->name('complete-invitation');
});

// User Support Ticket Routes
Route::middleware(['auth', 'verified'])->prefix('support')->name('support.')->group(function () {
    Route::get('/', [\App\Http\Controllers\User\SupportTicketController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\User\SupportTicketController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\User\SupportTicketController::class, 'store'])->name('store');
    Route::get('/{ticket}', [\App\Http\Controllers\User\SupportTicketController::class, 'show'])->name('show');
    Route::post('/{ticket}/reply', [\App\Http\Controllers\User\SupportTicketController::class, 'reply'])->name('reply');
    Route::post('/{ticket}/close', [\App\Http\Controllers\User\SupportTicketController::class, 'close'])->name('close');
    Route::post('/{ticket}/rate', [\App\Http\Controllers\User\SupportTicketController::class, 'rate'])->name('rate');
});

// User Appointment Routes
Route::middleware(['auth', 'verified'])->prefix('appointments')->name('appointments.')->group(function () {
    Route::get('/', [\App\Http\Controllers\User\AppointmentController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\User\AppointmentController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\User\AppointmentController::class, 'store'])->name('store');
    Route::get('/{appointment}', [\App\Http\Controllers\User\AppointmentController::class, 'show'])->name('show');
    Route::post('/{appointment}/cancel', [\App\Http\Controllers\User\AppointmentController::class, 'cancel'])->name('cancel');
    Route::post('/{appointment}/reschedule', [\App\Http\Controllers\User\AppointmentController::class, 'reschedule'])->name('reschedule');
});

// User FAQ Routes
Route::middleware(['auth', 'verified'])->prefix('faqs')->name('faqs.')->group(function () {
    Route::get('/', [\App\Http\Controllers\User\FaqController::class, 'index'])->name('index');
    Route::post('/{faq}/helpful', [\App\Http\Controllers\User\FaqController::class, 'helpful'])->name('helpful');
    Route::post('/{faq}/not-helpful', [\App\Http\Controllers\User\FaqController::class, 'notHelpful'])->name('not-helpful');
});

// Public Events Routes
Route::prefix('events')->name('events.')->group(function () {
    Route::get('/', [\App\Http\Controllers\User\EventController::class, 'index'])->name('index');
    Route::get('/{slug}', [\App\Http\Controllers\User\EventController::class, 'show'])->name('show');
});

// Public Directory Routes
Route::prefix('directory')->name('directory.')->group(function () {
    Route::get('/', [\App\Http\Controllers\User\DirectoryController::class, 'index'])->name('index');
    Route::get('/category/{slug}', [\App\Http\Controllers\User\DirectoryController::class, 'byCategory'])->name('category');
    Route::get('/search', [\App\Http\Controllers\User\DirectoryController::class, 'search'])->name('search');
    Route::get('/{slug}', [\App\Http\Controllers\User\DirectoryController::class, 'show'])->name('show');
});

// Public Agency Routes
Route::prefix('agencies')->name('agencies.')->group(function () {
    Route::get('/', [\App\Http\Controllers\AgencyPublicController::class, 'index'])->name('index');
    Route::get('/{slug}', [\App\Http\Controllers\AgencyPublicController::class, 'show'])->name('show');
    Route::post('/{agency}/review', [\App\Http\Controllers\AgencyPublicController::class, 'submitReview'])->name('review');
});

// Public Pages Routes (Terms, Privacy, About, etc.)
Route::get('/page/{slug}', [\App\Http\Controllers\User\PageController::class, 'show'])->name('page.show');











