<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceApplication;
use App\Models\ServiceModule;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceApplicationController extends Controller
{
    /**
     * Display a listing of service applications
     */
    public function index(Request $request)
    {
        $query = ServiceApplication::with(['user', 'serviceModule', 'quotes'])
            ->latest();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('application_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('serviceModule', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('slug', 'like', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Service module filter
        if ($request->filled('service_module_id')) {
            $query->where('service_module_id', $request->service_module_id);
        }

        $applications = $query->paginate(20)->withQueryString();

        // Add quotes count
        $applications->getCollection()->transform(function ($application) {
            $application->quotes_count = $application->quotes()->count();
            return $application;
        });

        // Calculate stats
        $stats = [
            'total' => ServiceApplication::count(),
            'pending' => ServiceApplication::where('status', 'pending')->count(),
            'quoted' => ServiceApplication::where('status', 'quoted')->count(),
            'accepted' => ServiceApplication::where('status', 'accepted')->count(),
            'in_progress' => ServiceApplication::where('status', 'in_progress')->count(),
            'completed' => ServiceApplication::where('status', 'completed')->count(),
            'cancelled' => ServiceApplication::where('status', 'cancelled')->count(),
        ];

        return Inertia::render('Admin/ServiceApplications/Index', [
            'applications' => $applications,
            'stats' => $stats,
            'filters' => $request->only(['search', 'status', 'service_module_id']),
            'serviceModules' => ServiceModule::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    /**
     * Display the specified service application
     */
    public function show(ServiceApplication $serviceApplication)
    {
        $serviceApplication->load([
            'user',
            'serviceModule',
            'assignedAgency'
        ]);

        $quotes = $serviceApplication->quotes()
            ->with('agency')
            ->latest()
            ->get();

        return Inertia::render('Admin/ServiceApplications/Show', [
            'application' => $serviceApplication,
            'quotes' => $quotes,
        ]);
    }

    /**
     * Update the status of a service application
     */
    public function updateStatus(Request $request, ServiceApplication $serviceApplication)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,quoted,accepted,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $serviceApplication->update($validated);

        return redirect()->back()->with('success', 'Application status updated successfully.');
    }

    /**
     * Delete a service application
     */
    public function destroy(ServiceApplication $serviceApplication)
    {
        $serviceApplication->delete();

        return redirect()->route('admin.service-applications.index')
            ->with('success', 'Application deleted successfully.');
    }

    /**
     * Export service applications
     */
    public function export(Request $request)
    {
        $query = ServiceApplication::with(['user', 'serviceModule', 'quotes'])
            ->latest();

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('application_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('service_module_id')) {
            $query->where('service_module_id', $request->service_module_id);
        }

        $applications = $query->get();

        // Generate CSV
        $filename = 'service-applications-' . date('Y-m-d-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($applications) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Application Number',
                'Service',
                'User Name',
                'User Email',
                'Status',
                'Quotes Count',
                'Created At',
                'Updated At',
            ]);

            // CSV data
            foreach ($applications as $application) {
                fputcsv($file, [
                    $application->application_number,
                    $application->serviceModule->name ?? 'N/A',
                    $application->user->name ?? 'N/A',
                    $application->user->email ?? 'N/A',
                    $application->status,
                    $application->quotes()->count(),
                    $application->created_at->format('Y-m-d H:i:s'),
                    $application->updated_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

