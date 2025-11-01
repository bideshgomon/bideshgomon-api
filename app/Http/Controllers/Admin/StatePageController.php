<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\State;
use Inertia\Inertia;

class StatePageController extends Controller
{
    /**
     * Display the list of states.
     * This single page will handle list, create, and edit.
     */
    public function index()
    {
        return Inertia::render('Admin/States/Index', [
            // Pass the paginated list of states, including their parent country
            'states' => State::with('country')
                ->latest()
                ->paginate(10),

            // Pass all countries for the modal's <select> dropdown
            'countries' => Country::orderBy('name')->get(['id', 'name']),
        ]);
    }
}
