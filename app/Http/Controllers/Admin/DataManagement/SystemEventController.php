<?php

namespace App\Http\Controllers\Admin\DataManagement;

use App\Http\Controllers\Controller;
use App\Models\SystemEvent;
use App\Models\User;
use App\Traits\BulkUploadable;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SystemEventController extends Controller
{
    use BulkUploadable;

    public function index(Request $request)
    {
        $query = SystemEvent::query();

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('event_type', 'LIKE', "%{$search}%")
                  ->orWhere('target_type', 'LIKE', "%{$search}%")
                  ->orWhere('target_id', 'LIKE', "%{$search}%");
            });
        }

        // Filter by event type
        if ($request->has('event_type') && $request->event_type !== '') {
            $query->where('event_type', $request->event_type);
        }

        // Filter by target type
        if ($request->has('target_type') && $request->target_type !== '') {
            $query->where('target_type', $request->target_type);
        }

        // Filter by user
        if ($request->has('user_id') && $request->user_id !== '') {
            $query->where('user_id', $request->user_id);
        }

        // Sort
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $events = $query->paginate(20);

        // Get unique event types and target types for filters
        $eventTypes = SystemEvent::distinct()->pluck('event_type');
        $targetTypes = SystemEvent::distinct()->whereNotNull('target_type')->pluck('target_type');

        return Inertia::render('Admin/DataManagement/SystemEvents/Index', [
            'events' => $events,
            'filters' => $request->only(['search', 'event_type', 'target_type', 'user_id', 'sort', 'direction']),
            'eventTypes' => $eventTypes,
            'targetTypes' => $targetTypes,
        ]);
    }

    public function create()
    {
        $users = User::select('id', 'name', 'email')->orderBy('name')->get();
        
        return Inertia::render('Admin/DataManagement/SystemEvents/Create', [
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_type' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'target_type' => 'nullable|string|max:255',
            'target_id' => 'nullable|integer',
            'data' => 'nullable|json',
        ]);

        // Decode data JSON
        if (isset($validated['data'])) {
            $validated['data'] = json_decode($validated['data'], true);
        }

        SystemEvent::create($validated);

        return redirect()
            ->route('admin.data.system-events.index')
            ->with('success', 'System event created successfully.');
    }

    public function edit(SystemEvent $systemEvent)
    {
        $users = User::select('id', 'name', 'email')->orderBy('name')->get();
        
        return Inertia::render('Admin/DataManagement/SystemEvents/Edit', [
            'event' => $systemEvent,
            'users' => $users,
        ]);
    }

    public function update(Request $request, SystemEvent $systemEvent)
    {
        $validated = $request->validate([
            'event_type' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'target_type' => 'nullable|string|max:255',
            'target_id' => 'nullable|integer',
            'data' => 'nullable|json',
        ]);

        // Decode data JSON
        if (isset($validated['data'])) {
            $validated['data'] = json_decode($validated['data'], true);
        }

        $systemEvent->update($validated);

        return redirect()
            ->route('admin.data.system-events.index')
            ->with('success', 'System event updated successfully.');
    }

    public function destroy(SystemEvent $systemEvent)
    {
        $systemEvent->delete();

        return redirect()
            ->route('admin.data.system-events.index')
            ->with('success', 'System event deleted successfully.');
    }

    // BulkUploadable trait implementation
    protected function getModelClass(): string
    {
        return SystemEvent::class;
    }

    protected function getRequiredColumns(): array
    {
        return ['event_type'];
    }

    protected function getOptionalColumns(): array
    {
        return ['user_id', 'target_type', 'target_id', 'data'];
    }

    protected function getTemplateColumns(): array
    {
        return array_merge($this->getRequiredColumns(), $this->getOptionalColumns());
    }

    protected function getValidationRules(): array
    {
        return [
            'event_type' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'target_type' => 'nullable|string|max:255',
            'target_id' => 'nullable|integer',
            'data' => 'nullable|string',
        ];
    }

    protected function saveRecord(array $data)
    {
        return SystemEvent::create($data);
    }

    protected function getColumnDescriptions(): array
    {
        return [
            'event_type' => 'Event type identifier (e.g., user.login, job.created, payment.processed)',
            'user_id' => 'User ID who triggered the event (optional)',
            'target_type' => 'Type of target entity (e.g., Job, Payment, Application)',
            'target_id' => 'ID of target entity (optional)',
            'data' => 'Additional event data as JSON (optional)',
        ];
    }

    protected function getSampleData(): array
    {
        return [
            [
                'event_type' => 'user.login',
                'user_id' => '1',
                'target_type' => '',
                'target_id' => '',
                'data' => '{"ip":"192.168.1.1","user_agent":"Mozilla/5.0"}',
            ],
            [
                'event_type' => 'job.created',
                'user_id' => '2',
                'target_type' => 'Job',
                'target_id' => '123',
                'data' => '{"title":"Software Engineer","company":"Tech Corp"}',
            ],
            [
                'event_type' => 'payment.processed',
                'user_id' => '3',
                'target_type' => 'Payment',
                'target_id' => '456',
                'data' => '{"amount":5000,"currency":"BDT","method":"bkash"}',
            ],
            [
                'event_type' => 'document.verified',
                'user_id' => '',
                'target_type' => 'Document',
                'target_id' => '789',
                'data' => '{"document_type":"passport","verified_by":"admin"}',
            ],
        ];
    }

    protected function validateRow(array $row, int $rowNumber): ?string
    {
        if (isset($row['user_id']) && !empty($row['user_id'])) {
            if (!User::where('id', $row['user_id'])->exists()) {
                return "Row {$rowNumber}: User with ID '{$row['user_id']}' does not exist";
            }
        }

        return null;
    }

    protected function transformRowData(array $row): array
    {
        $data = [
            'event_type' => trim($row['event_type']),
            'user_id' => !empty($row['user_id']) ? (int)$row['user_id'] : null,
            'target_type' => !empty($row['target_type']) ? trim($row['target_type']) : null,
            'target_id' => !empty($row['target_id']) ? (int)$row['target_id'] : null,
            'data' => !empty($row['data']) ? json_decode($row['data'], true) : null,
        ];

        return $data;
    }

    protected function getExportQuery(Request $request)
    {
        return SystemEvent::orderBy('created_at', 'desc');
    }

    protected function getExportColumns(): array
    {
        return ['id', 'event_type', 'user_id', 'target_type', 'target_id', 'data', 'created_at'];
    }

    protected function prepareExportRow($model): array
    {
        return [
            'id' => $model->id,
            'event_type' => $model->event_type,
            'user_id' => $model->user_id ?? '',
            'target_type' => $model->target_type ?? '',
            'target_id' => $model->target_id ?? '',
            'data' => !empty($model->data) ? json_encode($model->data) : '',
            'created_at' => $model->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
