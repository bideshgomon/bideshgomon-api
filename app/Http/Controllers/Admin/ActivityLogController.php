<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with(['causer', 'subject'])
            ->latest();

        // Filter by user
        if ($request->user_id) {
            $query->where('causer_id', $request->user_id);
        }

        // Filter by subject type
        if ($request->subject_type) {
            $query->where('subject_type', $request->subject_type);
        }

        // Filter by event
        if ($request->event) {
            $query->where('event', $request->event);
        }

        // Filter by description
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('description', 'like', '%' . $request->search . '%')
                  ->orWhere('properties', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by date range
        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $activities = $query->paginate(50);

        // Get unique subject types for filter
        $subjectTypes = Activity::select('subject_type')
            ->distinct()
            ->whereNotNull('subject_type')
            ->pluck('subject_type')
            ->map(function ($type) {
                return [
                    'value' => $type,
                    'label' => class_basename($type)
                ];
            });

        // Get unique events for filter
        $events = Activity::select('event')
            ->distinct()
            ->whereNotNull('event')
            ->pluck('event')
            ->map(function ($event) {
                return [
                    'value' => $event,
                    'label' => ucfirst($event)
                ];
            });

        return Inertia::render('Admin/ActivityLog/Index', [
            'activities' => $activities,
            'filters' => $request->only(['user_id', 'subject_type', 'event', 'search', 'from_date', 'to_date']),
            'subjectTypes' => $subjectTypes,
            'events' => $events
        ]);
    }

    public function show(Activity $activity)
    {
        $activity->load(['causer', 'subject']);

        return Inertia::render('Admin/ActivityLog/Show', [
            'activity' => [
                'id' => $activity->id,
                'log_name' => $activity->log_name,
                'description' => $activity->description,
                'subject_type' => $activity->subject_type,
                'subject_id' => $activity->subject_id,
                'event' => $activity->event,
                'causer' => $activity->causer ? [
                    'id' => $activity->causer->id,
                    'name' => $activity->causer->name,
                    'email' => $activity->causer->email
                ] : null,
                'properties' => $activity->properties,
                'batch_uuid' => $activity->batch_uuid,
                'created_at' => $activity->created_at,
                'updated_at' => $activity->updated_at
            ]
        ]);
    }
}
