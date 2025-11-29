<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\AgencyReview;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AgencyPublicController extends Controller
{
    public function index(Request $request)
    {
        $query = Agency::with(['countryAssignments.country', 'agencyType'])
            ->active()
            ->verified();

        // Filters
        if ($request->country) {
            $query->whereHas('countryAssignments', function ($q) use ($request) {
                $q->where('country_id', $request->country);
            });
        }

        if ($request->business_type) {
            $query->where('business_type', $request->business_type);
        }

        if ($request->min_rating) {
            $query->withRating($request->min_rating);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('city', 'like', '%' . $request->search . '%');
            });
        }

        // Sorting
        switch ($request->sort) {
            case 'rating':
                $query->orderBy('average_rating', 'desc');
                break;
            case 'reviews':
                $query->orderBy('total_reviews', 'desc');
                break;
            case 'clients':
                $query->orderBy('total_clients', 'desc');
                break;
            case 'newest':
                $query->latest();
                break;
            default:
                $query->featured()->orderBy('average_rating', 'desc');
        }

        $agencies = $query->paginate(12);

        return Inertia::render('Public/Agencies/Index', [
            'agencies' => $agencies,
            'filters' => $request->only(['country', 'business_type', 'min_rating', 'search', 'sort']),
        ]);
    }

    public function show($slug)
    {
        $agency = Agency::where('slug', $slug)
            ->active()
            ->verified()
            ->with([
                'agencyType',
                'teamMembers' => function ($query) {
                    $query->visible()->ordered();
                },
                'reviews' => function ($query) {
                    $query->visible()->with('user')->latest();
                },
                'countryAssignments.country'
            ])
            ->firstOrFail();

        $relatedAgencies = Agency::active()
            ->verified()
            ->where('id', '!=', $agency->id)
            ->where('agency_type_id', $agency->agency_type_id)
            ->with('agencyType')
            ->withRating(4)
            ->limit(4)
            ->get();

        return Inertia::render('Public/Agencies/Show', [
            'agency' => $agency,
            'relatedAgencies' => $relatedAgencies,
        ]);
    }

    public function submitReview(Request $request, Agency $agency)
    {
        if (!auth()->check()) {
            return back()->with('error', 'Please login to submit a review.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|min:10|max:1000',
            'service_application_id' => 'nullable|exists:service_applications,id',
        ]);

        $validated['agency_id'] = $agency->id;
        $validated['user_id'] = auth()->id();

        // Check if user already reviewed this agency for this application
        $existingReview = AgencyReview::where('agency_id', $agency->id)
            ->where('user_id', auth()->id())
            ->where('service_application_id', $validated['service_application_id'])
            ->first();

        if ($existingReview) {
            return back()->with('error', 'You have already reviewed this agency.');
        }

        AgencyReview::create($validated);

        // Update agency rating
        $agency->updateRating();

        return back()->with('success', 'Review submitted successfully and is pending approval.');
    }
}
