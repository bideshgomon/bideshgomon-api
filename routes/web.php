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
use App\Http\Controllers\VisaApplicationController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\OnboardingController;
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

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

// Public profile route (no auth required)
Route::get('/profile/{slug}', [\App\Http\Controllers\PublicProfileController::class, 'show'])
    ->name('profile.public.show');

Route::get('/dashboard', function () {
    $user = auth()->user();
    $profile = $user->userProfile;
    
    // Calculate stats
    $stats = [
        'education_count' => $user->educations()->count(),
        'experience_count' => $user->workExperiences()->count(),
        'family_count' => $user->familyMembers()->count(),
        'languages_count' => $user->languages()->count(),
        'profile_strength' => $user->educations()->count() > 0 && $user->workExperiences()->count() > 0 ? 'Strong' : 'Basic',
    ];
    
    // Calculate profile completion (simplified)
    $completion = 0;
    if ($user->name) $completion += 15;
    if ($user->email) $completion += 15;
    if ($user->userProfile) $completion += 20;
    if ($user->educations()->count() > 0) $completion += 15;
    if ($user->workExperiences()->count() > 0) $completion += 15;
    if ($user->familyMembers()->count() > 0) $completion += 10;
    if ($user->languages()->count() > 0) $completion += 10;
    
    // Generate smart suggestions based on profile completion
    $suggestions = [];
    
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
    
    if ($profile && $profile->passport_number && $user->educations()->count() > 0) {
        $recommendedServices[] = [
            'name' => 'Apply for Student Visa',
            'description' => 'You have education records - perfect for study abroad applications',
            'route' => 'visa.create',
            'color' => 'blue',
        ];
    }
    
    if ($user->workExperiences()->count() > 0 && $profile && $profile->monthly_income_bdt) {
        $recommendedServices[] = [
            'name' => 'Browse Work Visas',
            'description' => 'Your work experience qualifies you for employment abroad',
            'route' => 'visa.create',
            'color' => 'purple',
        ];
    }
    
    return Inertia::render('Dashboard', [
        'stats' => $stats,
        'profileCompletion' => min($completion, 100),
        'recentActivity' => [],
        'suggestions' => $suggestions,
        'recommendedServices' => $recommendedServices,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
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
        
        // Skills
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
    
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/details', [ProfileController::class, 'updateDetails'])->name('profile.update.details');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

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

    // Wallet routes
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::get('/wallet/transactions', [WalletController::class, 'transactions'])->name('wallet.transactions');
    Route::post('/wallet/add-funds', [WalletController::class, 'addFunds'])->name('wallet.add-funds');
    Route::post('/wallet/withdraw', [WalletController::class, 'withdraw'])->name('wallet.withdraw');

    // Referral routes
    Route::get('/referrals', [ReferralController::class, 'index'])->name('referral.index');
    Route::get('/referrals/list', [ReferralController::class, 'referrals'])->name('referral.referrals');
    Route::get('/referrals/rewards', [ReferralController::class, 'rewards'])->name('referral.rewards');

    // Profile Assessment routes
    Route::prefix('profile/assessment')->name('profile.assessment.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ProfileAssessmentController::class, 'show'])->name('show');
        Route::post('/generate', [\App\Http\Controllers\ProfileAssessmentController::class, 'generate'])->name('generate');
        Route::get('/recommendations', [\App\Http\Controllers\ProfileAssessmentController::class, 'recommendations'])->name('recommendations');
        Route::get('/score-breakdown', [\App\Http\Controllers\ProfileAssessmentController::class, 'scoreBreakdown'])->name('score-breakdown');
        Route::get('/visa-eligibility', [\App\Http\Controllers\ProfileAssessmentController::class, 'visaEligibility'])->name('visa-eligibility');
    });

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

    // CV Builder routes
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

    // Job Posting routes
    Route::prefix('jobs')->name('jobs.')->group(function () {
        Route::get('/', [JobController::class, 'index'])->name('index');
        Route::get('/{id}', [JobController::class, 'show'])->name('show');
        Route::post('/{id}/apply', [JobController::class, 'apply'])->name('apply');
        Route::get('/my/applications', [JobController::class, 'myApplications'])->name('my-applications');
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

    // Visa Application routes
    Route::prefix('services/visa')->name('visa.')->group(function () {
        Route::get('/', [VisaApplicationController::class, 'index'])->name('index');
        Route::get('/apply', [VisaApplicationController::class, 'create'])->name('create');
        Route::post('/', [VisaApplicationController::class, 'store'])->name('store');
        Route::get('/my-applications', [VisaApplicationController::class, 'myApplications'])->name('my-applications');
        Route::get('/{application}', [VisaApplicationController::class, 'show'])->name('show');
        Route::post('/{application}/documents', [VisaApplicationController::class, 'uploadDocument'])->name('upload-document');
        Route::get('/{application}/payment', [VisaApplicationController::class, 'payment'])->name('payment');
        Route::post('/{application}/payment', [VisaApplicationController::class, 'processPayment'])->name('process-payment');
        Route::post('/{application}/cancel', [VisaApplicationController::class, 'cancel'])->name('cancel');
    });

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

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Service Management Dashboard
    Route::get('/services', [\App\Http\Controllers\Admin\ServiceManagementController::class, 'index'])->name('services.index');
    
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
});

// Agency routes
Route::middleware(['auth', 'role:agency'])->prefix('agency')->name('agency.')->group(function () {
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
    Route::resource('blog.posts', \App\Http\Controllers\Admin\BlogPostController::class);
    Route::resource('blog.categories', \App\Http\Controllers\Admin\BlogCategoryController::class)->except(['show', 'create', 'edit']);
    Route::resource('blog.tags', \App\Http\Controllers\Admin\BlogTagController::class)->except(['show', 'create', 'edit']);
});

// Admin Routes
Route::prefix('admin')->middleware(['auth'])->group(function () {
    // Redirect /admin to /admin/dashboard
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    })->name('admin');
    
    Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('admin.dashboard');
    
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
    Route::post('/jobs/bulk-delete', [AdminJobPostingController::class, 'bulkDelete'])->name('admin.jobs.bulk-delete');
    Route::post('/jobs/bulk-update-status', [AdminJobPostingController::class, 'bulkUpdateStatus'])->name('admin.jobs.bulk-update-status');
    
    // Job Applications Management
    Route::get('/applications', [AdminJobApplicationController::class, 'index'])->name('admin.applications.index');
    Route::get('/applications/{id}', [AdminJobApplicationController::class, 'show'])->name('admin.applications.show');
    Route::post('/applications/{id}/update-status', [AdminJobApplicationController::class, 'updateStatus'])->name('admin.applications.update-status');
    Route::post('/applications/bulk-update-status', [AdminJobApplicationController::class, 'bulkUpdateStatus'])->name('admin.applications.bulk-update-status');
    Route::get('/applications/export', [AdminJobApplicationController::class, 'export'])->name('admin.applications.export');
    
    // User Management
    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/{id}', [AdminUserController::class, 'show'])->name('admin.users.show');
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
});










