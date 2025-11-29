<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\AgencyTeamMember;
use App\Models\AgencyType;
use App\Models\Country;
use App\Models\Language;
use App\Models\ServiceModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function show()
    {
        $agency = auth()->user()->agency;

        if (!$agency) {
            return redirect()->route('agency.dashboard')->with('error', 'Agency profile not found.');
        }

        $agency->load(['teamMembers' => function ($query) {
            $query->ordered();
        }, 'reviews' => function ($query) {
            $query->visible()->with('user')->latest()->limit(10);
        }]);

        return Inertia::render('Agency/Profile/Show', [
            'agency' => $agency,
            'profileCompletion' => $this->calculateProfileCompletion($agency),
        ]);
    }

    public function edit()
    {
        $agency = auth()->user()->agency;

        if (!$agency) {
            return redirect()->route('agency.dashboard')->with('error', 'Agency profile not found.');
        }

        $agency->load('teamMembers');

        // Group service modules by category for better UX
        $serviceModules = ServiceModule::active()
            ->orderBy('id')
            ->get(['id', 'name', 'slug', 'icon', 'color'])
            ->groupBy(function ($service) {
                // Categorize services based on name patterns
                $name = strtolower($service->name);
                
                if (str_contains($name, 'visa')) return 'Visa Services';
                if (in_array($service->id, [8, 9, 10, 11, 12, 13, 28])) return 'Travel & Booking';
                if (in_array($service->id, [14, 15, 16, 17])) return 'Education Services';
                if (in_array($service->id, [18, 19, 20, 21, 22])) return 'Employment Services';
                if (in_array($service->id, [23, 24, 25, 26, 27])) return 'Document Services';
                
                return 'Support Services';
            });

        return Inertia::render('Agency/Profile/Edit', [
            'agency' => $agency,
            'countries' => Country::orderBy('name')->pluck('name'),
            'languages' => Language::orderBy('name')->pluck('name'),
            'agencyTypes' => AgencyType::active()
                ->orderBy('display_order')
                ->get(['id', 'name', 'slug', 'description', 'icon', 'color', 'allowed_service_modules']),
            'serviceModules' => $serviceModules,
        ]);
    }

    public function update(Request $request)
    {
        $agency = auth()->user()->agency;

        if (!$agency) {
            return back()->with('error', 'Agency profile not found.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'registration_number' => 'nullable|string|max:100',
            'phone' => 'required|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'description' => 'required|string|max:2000',
            'meta_description' => 'nullable|string|max:300',
            'website' => 'nullable|url|max:255',
            'facebook_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'agency_type_id' => 'required|exists:agency_types,id',
            'business_type' => 'nullable|string', // Keep for backward compatibility
            'established_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'license_number' => 'nullable|string|max:100',
            'license_expiry' => 'nullable|date|after:today',
            'services_offered' => 'nullable|array',
            'countries_expertise' => 'nullable|array',
            'languages_spoken' => 'nullable|array',
            'team_size' => 'nullable|integer|min:1',
            'office_hours' => 'nullable|string|max:500',
            'certifications' => 'nullable|array',
            'awards' => 'nullable|array',
        ]);

        $agency->update($validated);

        // Check if profile is complete
        if ($agency->isProfileComplete() && !$agency->profile_completed_at) {
            $agency->update(['profile_completed_at' => now()]);
        }

        return back()->with('success', 'Agency profile updated successfully.');
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $agency = auth()->user()->agency;

        if (!$agency) {
            return back()->with('error', 'Agency profile not found.');
        }

        // Delete old logo
        if ($agency->logo_path) {
            Storage::delete($agency->logo_path);
        }

        $path = $request->file('logo')->store('agency-logos', 'public');

        $agency->update(['logo_path' => $path]);

        return back()->with('success', 'Logo uploaded successfully.');
    }

    public function uploadOfficeImages(Request $request)
    {
        $request->validate([
            'images' => 'required|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $agency = auth()->user()->agency;

        if (!$agency) {
            return back()->with('error', 'Agency profile not found.');
        }

        $existingImages = $agency->office_images ?? [];
        $newImages = [];

        foreach ($request->file('images') as $image) {
            $path = $image->store('agency-offices', 'public');
            $newImages[] = $path;
        }

        $agency->update([
            'office_images' => array_merge($existingImages, $newImages)
        ]);

        return back()->with('success', 'Office images uploaded successfully.');
    }

    public function deleteOfficeImage(Request $request)
    {
        $request->validate([
            'image_path' => 'required|string',
        ]);

        $agency = auth()->user()->agency;

        if (!$agency) {
            return back()->with('error', 'Agency profile not found.');
        }

        $officeImages = $agency->office_images ?? [];
        $imagePath = $request->image_path;

        if (in_array($imagePath, $officeImages)) {
            Storage::delete($imagePath);
            $officeImages = array_values(array_diff($officeImages, [$imagePath]));
            $agency->update(['office_images' => $officeImages]);
        }

        return back()->with('success', 'Office image deleted successfully.');
    }

    private function calculateProfileCompletion($agency)
    {
        $fields = [
            'name', 'company_name', 'phone', 'email', 'address', 'city', 'country',
            'description', 'business_type', 'established_year', 'website',
            'services_offered', 'logo_path', 'team_size'
        ];

        $completed = 0;
        foreach ($fields as $field) {
            if (!empty($agency->$field)) {
                $completed++;
            }
        }

        return round(($completed / count($fields)) * 100);
    }

    private function getServiceOptions()
    {
        return [
            'visa_application' => 'Visa Application',
            'work_permit' => 'Work Permit Processing',
            'student_visa' => 'Student Visa',
            'tourist_visa' => 'Tourist Visa',
            'pr_application' => 'Permanent Residency',
            'job_placement' => 'Job Placement',
            'university_admission' => 'University Admission',
            'travel_booking' => 'Travel Booking',
            'document_translation' => 'Document Translation',
            'interview_preparation' => 'Interview Preparation',
            'language_training' => 'Language Training',
            'accommodation' => 'Accommodation Assistance',
        ];
    }
}
