<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminImpersonationLog;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminImpersonationLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AdminImpersonationLog::with([
            'impersonator:id,name',
            'target:id,name'
        ])->latest('started_at');

        // Filters
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('ended_at');
            } elseif ($request->status === 'ended') {
                $query->whereNotNull('ended_at');
            }
        }
        if ($request->filled('admin_id')) {
            $query->where('impersonator_id', $request->admin_id);
        }
        if ($request->filled('target_id')) {
            $query->where('target_user_id', $request->target_id);
        }
        if ($request->filled('from')) {
            $query->whereDate('started_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('started_at', '<=', $request->to);
        }

        $logs = $query->paginate(20)->withQueryString();

        $admins = User::whereHas('role', fn($r) => $r->where('slug', 'admin'))
            ->select('id','name')
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/Impersonations/Index', [
            'logs' => $logs->through(function ($log) {
                return [
                    'id' => $log->id,
                    'impersonator' => $log->impersonator ? [
                        'id' => $log->impersonator->id,
                        'name' => $log->impersonator->name,
                    ] : null,
                    'target' => $log->target ? [
                        'id' => $log->target->id,
                        'name' => $log->target->name,
                    ] : null,
                    'purpose' => $log->purpose,
                    'started_at' => $log->started_at,
                    'ended_at' => $log->ended_at,
                    'duration_minutes' => $log->duration_minutes,
                    'status' => $log->ended_at ? 'ended' : 'active',
                ];
            }),
            'filters' => [
                'status' => $request->status,
                'admin_id' => $request->admin_id,
                'target_id' => $request->target_id,
                'from' => $request->from,
                'to' => $request->to,
            ],
            'admins' => $admins,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $fileName = 'impersonation_logs_' . now()->format('Ymd_His') . '.csv';

        $response = new StreamedResponse(function () use ($request) {
            $handle = fopen('php://output', 'w');
            // Header row
            fputcsv($handle, ['ID','Admin','Target User','Purpose','Started At','Ended At','Duration Minutes','Status']);

            $query = AdminImpersonationLog::with(['impersonator:id,name','target:id,name'])->latest('started_at');
            if ($request->filled('status')) {
                if ($request->status === 'active') {
                    $query->whereNull('ended_at');
                } elseif ($request->status === 'ended') {
                    $query->whereNotNull('ended_at');
                }
            }
            $query->chunk(500, function ($logs) use ($handle) {
                foreach ($logs as $log) {
                    fputcsv($handle, [
                        $log->id,
                        $log->impersonator?->name,
                        $log->target?->name,
                        $log->purpose,
                        $log->started_at?->format('Y-m-d H:i:s'),
                        $log->ended_at?->format('Y-m-d H:i:s'),
                        $log->duration_minutes,
                        $log->ended_at ? 'ended' : 'active',
                    ]);
                }
            });
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$fileName.'"');

        return $response;
    }
}
