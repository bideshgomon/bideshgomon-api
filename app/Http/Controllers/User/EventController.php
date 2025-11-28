<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EventController extends Controller
{
    /**
     * Display public events listing
     */
    public function index(Request $request)
    {
        $query = Event::published();

        // Search
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->byType($request->type);
        }

        // Filter by time
        if ($request->has('time')) {
            if ($request->time === 'upcoming') {
                $query->upcoming();
            } elseif ($request->time === 'past') {
                $query->past();
            }
        } else {
            // Default to upcoming events
            $query->upcoming();
        }

        $events = $query->paginate(12);
        $featuredEvents = Event::published()->featured()->upcoming()->take(3)->get();

        return Inertia::render('User/Events/Index', [
            'events' => $events,
            'featuredEvents' => $featuredEvents,
            'filters' => $request->only(['search', 'type', 'time']),
        ]);
    }

    /**
     * Display the specified event
     */
    public function show($slug)
    {
        $event = Event::where('slug', $slug)->published()->firstOrFail();

        return Inertia::render('User/Events/Show', [
            'event' => $event,
        ]);
    }
}
