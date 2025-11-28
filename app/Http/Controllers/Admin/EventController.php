<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Display a listing of events
     */
    public function index(Request $request)
    {
        $query = Event::query();

        // Search
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->byType($request->type);
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            } elseif ($request->status === 'upcoming') {
                $query->upcoming();
            } elseif ($request->status === 'past') {
                $query->past();
            }
        }

        $events = $query->latest()->paginate(20);

        return Inertia::render('Admin/Events/Index', [
            'events' => $events,
            'filters' => $request->only(['search', 'type', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new event
     */
    public function create()
    {
        return Inertia::render('Admin/Events/Create');
    }

    /**
     * Store a newly created event
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:events,slug',
            'description' => 'required|string',
            'event_type' => 'required|in:seminar,workshop,fair,consultation,webinar',
            'event_date' => 'required|date|after_or_equal:today',
            'event_time' => 'required|date_format:H:i',
            'end_date' => 'nullable|date|after_or_equal:event_date',
            'end_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:500',
            'is_online' => 'boolean',
            'meeting_link' => 'nullable|url|max:500',
            'image' => 'nullable|image|max:2048',
            'max_participants' => 'nullable|integer|min:1',
            'registration_deadline' => 'nullable|date|before_or_equal:event_date',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ]);

        // Auto-generate slug if not provided
        if (!isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('events', 'public');
        }

        Event::create($validated);

        return redirect()->route('admin.events.index')->with('success', 'Event created successfully.');
    }

    /**
     * Display the specified event
     */
    public function show(Event $event)
    {
        return Inertia::render('Admin/Events/Show', [
            'event' => $event,
        ]);
    }

    /**
     * Show the form for editing the event
     */
    public function edit(Event $event)
    {
        return Inertia::render('Admin/Events/Edit', [
            'event' => $event,
        ]);
    }

    /**
     * Update the event
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:events,slug,' . $event->id,
            'description' => 'required|string',
            'event_type' => 'required|in:seminar,workshop,fair,consultation,webinar',
            'event_date' => 'required|date',
            'event_time' => 'required|date_format:H:i',
            'end_date' => 'nullable|date|after_or_equal:event_date',
            'end_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:500',
            'is_online' => 'boolean',
            'meeting_link' => 'nullable|url|max:500',
            'image' => 'nullable|image|max:2048',
            'max_participants' => 'nullable|integer|min:1',
            'registration_deadline' => 'nullable|date|before_or_equal:event_date',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ]);

        // Auto-generate slug if not provided
        if (!isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $validated['image'] = $request->file('image')->store('events', 'public');
        }

        $event->update($validated);

        return redirect()->route('admin.events.index')->with('success', 'Event updated successfully.');
    }

    /**
     * Remove the event
     */
    public function destroy(Event $event)
    {
        // Delete image
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }

        $event->delete();

        return redirect()->route('admin.events.index')->with('success', 'Event deleted successfully.');
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Event $event)
    {
        $event->update(['is_featured' => !$event->is_featured]);

        return back()->with('success', 'Event featured status updated.');
    }

    /**
     * Toggle published status
     */
    public function togglePublished(Event $event)
    {
        $event->update(['is_published' => !$event->is_published]);

        return back()->with('success', 'Event published status updated.');
    }
}
