<?php

namespace App\Http\Controllers;

use App\Models\CvTemplate;
use App\Models\UserCv;
use App\Models\Country;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class CvBuilderController extends Controller
{
    /**
     * Display CV templates listing
     */
    public function index()
    {
        $templates = CvTemplate::active()
            ->ordered()
            ->get()
            ->groupBy('category');

        $userCvs = UserCv::forUser(auth()->id())
            ->with('cvTemplate')
            ->latest()
            ->get();

        return Inertia::render('Services/CvBuilder/Index', [
            'templates' => $templates,
            'userCvs' => $userCvs,
        ]);
    }

    /**
     * Show CV template details (redirect to create with template)
     */
    public function showTemplate($slug)
    {
        $template = CvTemplate::where('slug', $slug)->active()->firstOrFail();

        return redirect()->route('cv-builder.create', ['template' => $template->id]);
    }

    /**
     * Show CV builder form
     */
    public function create(Request $request)
    {
        $templateId = $request->query('template');
        $template = CvTemplate::findOrFail($templateId);

        // Get user's profile data to pre-fill
        $user = auth()->user()->load([
            'profile',
            'educations',
            'workExperiences',
            'skills',
            'languages',
            'phoneNumbers',
            'wallet'
        ]);

        // Get user profile for additional data
        $userProfile = $user->profile;

        // Transform education data for CV format
        $educationData = $user->educations->map(function ($edu) {
            return [
                'degree' => $edu->degree ?? '',
                'institution' => $edu->institution_name ?? '',
                'field_of_study' => $edu->field_of_study ?? '',
                'start_date' => $edu->start_date ? date('Y-m', strtotime($edu->start_date)) : '',
                'end_date' => $edu->end_date ? date('Y-m', strtotime($edu->end_date)) : '',
                'grade' => $edu->gpa_or_grade ?? '',
                'description' => $edu->courses_completed ?? '',
            ];
        })->toArray();

        // Transform work experience data
        $experienceData = $user->workExperiences->map(function ($exp) {
            return [
                'job_title' => $exp->job_title ?? '',
                'company' => $exp->company_name ?? '',
                'location' => $exp->location ?? '',
                'start_date' => $exp->start_date ? date('Y-m', strtotime($exp->start_date)) : '',
                'end_date' => $exp->is_current ? '' : ($exp->end_date ? date('Y-m', strtotime($exp->end_date)) : ''),
                'is_current' => $exp->is_current ?? false,
                'description' => $exp->description ?? '',
            ];
        })->toArray();

        // Transform skills data
        $skillsData = $user->skills->map(function ($skill) {
            return [
                'name' => $skill->name ?? '',
                'level' => strtolower($skill->pivot->proficiency_level ?? 'intermediate'),
            ];
        })->toArray();

        // Transform languages data
        $languagesData = $user->languages->map(function ($lang) {
            return [
                'language' => $lang->language->name ?? $lang->language ?? '',
                'proficiency' => strtolower($lang->proficiency_level ?? 'intermediate'),
            ];
        })->toArray();

        // Get certifications from profile
        $certificationsData = [];
        if ($userProfile && $userProfile->certifications) {
            $certificationsData = is_array($userProfile->certifications) 
                ? $userProfile->certifications 
                : json_decode($userProfile->certifications, true) ?? [];
        }

        // Primary phone number
        $primaryPhone = $user->phoneNumbers->where('is_primary', true)->first();
        $phoneNumber = '';
        if ($primaryPhone) {
            $phoneNumber = $primaryPhone->phone_number;
        } elseif ($user->phoneNumbers->isNotEmpty()) {
            // If no primary phone, get the first phone number
            $phoneNumber = $user->phoneNumbers->first()->phone_number;
        }

        // Pre-fill data from profile
        $profileData = [
            'full_name' => $userProfile ? trim(($userProfile->first_name ?? '') . ' ' . ($userProfile->middle_name ?? '') . ' ' . ($userProfile->last_name ?? '')) : $user->name,
            'email' => $user->email,
            'phone' => $phoneNumber,
            'address' => $userProfile ? ($userProfile->present_address_line ?? '') : '',
            'city' => $userProfile ? ($userProfile->present_district ?? '') : '',
            'country_id' => $userProfile ? ($userProfile->country_id ?? null) : null,
            'linkedin_url' => $userProfile && $userProfile->social_links ? ($userProfile->social_links['linkedin'] ?? '') : '',
            'website_url' => $userProfile && $userProfile->social_links ? ($userProfile->social_links['website'] ?? '') : '',
            'professional_summary' => $userProfile ? ($userProfile->bio ?? '') : '',
        ];

        $countries = Country::orderBy('name')->get();

        return Inertia::render('Services/CvBuilder/Create', [
            'template' => $template,
            'user' => $user,
            'countries' => $countries,
            'profileData' => $profileData,
            'profileEducation' => $educationData,
            'profileExperience' => $experienceData,
            'profileSkills' => $skillsData,
            'profileLanguages' => $languagesData,
            'profileCertifications' => $certificationsData,
        ]);
    }

    /**
     * Store new CV
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cv_template_id' => 'required|exists:cv_templates,id',
            'title' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'city' => 'nullable|string|max:100',
            'country_id' => 'nullable|exists:countries,id',
            'address' => 'nullable|string',
            'linkedin_url' => 'nullable|url',
            'website_url' => 'nullable|url',
            'professional_summary' => 'required|string|min:50',
            'education' => 'required|array|min:1',
            'experience' => 'required|array|min:1',
            'skills' => 'required|array|min:1',
            'languages' => 'nullable|array',
            'certifications' => 'nullable|array',
        ]);

        // Check if premium template and process payment
        $template = CvTemplate::findOrFail($validated['cv_template_id']);
        if ($template->is_premium && $template->price > 0) {
            $user = auth()->user();
            $wallet = $user->wallet;
            
            if (!$wallet || $wallet->balance < $template->price) {
                return back()->withErrors([
                    'payment' => 'Insufficient wallet balance. Please add funds to your wallet.'
                ]);
            }
            
            // Deduct payment using WalletService
            $walletService = app(\App\Services\WalletService::class);
            try {
                $walletService->debitWallet(
                    $wallet,
                    (float) $template->price,
                    "Payment for {$template->name} CV Template",
                    'premium_cv_template',
                    $template->id
                );
            } catch (\Exception $e) {
                return back()->withErrors([
                    'payment' => 'Payment failed: ' . $e->getMessage()
                ]);
            }
        }

        $validated['user_id'] = auth()->user()->id;

        $cv = UserCv::create($validated);

        return redirect()->route('cv-builder.my-cvs')
            ->with('success', 'CV created successfully!');
    }

    /**
     * Show user's CVs
     */
    public function myCvs()
    {
        $cvs = UserCv::forUser(auth()->id())
            ->with(['cvTemplate', 'country'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Services/CvBuilder/MyCvs', [
            'cvs' => $cvs,
        ]);
    }

    /**
     * Show edit CV form
     */
    public function edit($id)
    {
        $cv = UserCv::where('id', $id)
            ->forUser(auth()->id())
            ->with(['cvTemplate', 'country'])
            ->firstOrFail();

        $countries = Country::orderBy('name')->get();

        return Inertia::render('Services/CvBuilder/Edit', [
            'cv' => $cv,
            'countries' => $countries,
        ]);
    }

    /**
     * Update CV
     */
    public function update(Request $request, $id)
    {
        $cv = UserCv::where('id', $id)
            ->forUser(auth()->id())
            ->firstOrFail();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'city' => 'nullable|string|max:255',
            'country_id' => 'nullable|exists:countries,id',
            'address' => 'nullable|string|max:500',
            'linkedin_url' => 'nullable|url|max:255',
            'website_url' => 'nullable|url|max:255',
            'professional_summary' => 'nullable|string',
            'education' => 'nullable|array',
            'experience' => 'nullable|array',
            'skills' => 'nullable|array',
            'languages' => 'nullable|array',
            'certifications' => 'nullable|array',
            'projects' => 'nullable|array',
            'references' => 'nullable|array',
        ]);

        $cv->update($validated);

        return redirect()->back()->with('success', 'CV updated successfully!');
    }

    /**
     * Delete CV
     */
    public function destroy($id)
    {
        $cv = UserCv::where('id', $id)
            ->forUser(auth()->id())
            ->firstOrFail();

        $cv->delete();

        return redirect()->route('cv-builder.my-cvs')
            ->with('success', 'CV deleted successfully!');
    }

    /**
     * Preview CV
     */
    public function preview($id)
    {
        $cv = UserCv::where('id', $id)
            ->forUser(auth()->id())
            ->with(['cvTemplate', 'country'])
            ->firstOrFail();

        $cv->incrementViewCount();

        return Inertia::render('Services/CvBuilder/Preview', [
            'cv' => $cv,
        ]);
    }

    /**
     * Download CV as PDF
     */
    public function download($id)
    {
        $cv = UserCv::with(['cvTemplate', 'country'])
            ->forUser(auth()->user()->id)
            ->findOrFail($id);

        // Get template for styling
        $template = $cv->cvTemplate;
        
        // Generate PDF
        $pdf = Pdf::loadView('cv-pdf-template', [
            'cv' => $cv,
            'template' => $template,
        ]);
        
        // Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');
        
        // Set options for better rendering
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        
        // Increment download count
        $cv->incrementDownloadCount();
        
        // Generate filename
        $filename = Str::slug($cv->title) . '-' . date('Y-m-d') . '.pdf';
        
        // Download PDF with proper headers
        return $pdf->download($filename);
    }
}
