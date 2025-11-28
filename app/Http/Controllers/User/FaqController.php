<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FaqController extends Controller
{
    /**
     * Display public FAQ page
     */
    public function index(Request $request)
    {
        $query = Faq::with('category')->published();

        // Search
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // Filter by category
        if ($request->has('category_id') && $request->category_id) {
            $query->where('faq_category_id', $request->category_id);
        }

        $faqs = $query->ordered()->get();
        $categories = FaqCategory::active()->ordered()->withCount('activeFaqs')->get();

        return Inertia::render('User/Faqs/Index', [
            'faqs' => $faqs,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category_id']),
        ]);
    }

    /**
     * Mark FAQ as helpful
     */
    public function helpful(Faq $faq)
    {
        $faq->markHelpful();

        return back()->with('success', 'Thank you for your feedback!');
    }

    /**
     * Mark FAQ as not helpful
     */
    public function notHelpful(Faq $faq)
    {
        $faq->markNotHelpful();

        return back()->with('success', 'Thank you for your feedback!');
    }
}
