<?php

namespace App\Http\Controllers\Admin\DataManagement;

use App\Http\Controllers\Controller;
use App\Models\SmartSuggestion;
use App\Models\User;
use App\Traits\BulkUploadable;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SmartSuggestionController extends Controller
{
    use BulkUploadable;

    public function index(Request $request)
    {
        $query = SmartSuggestion::with('user:id,name,email');

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('suggestion_type', 'LIKE', "%{$search}%")
                  ->orWhere('category', 'LIKE', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category') && $request->category !== '') {
            $query->where('category', $request->category);
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority !== '') {
            $query->where('priority', $request->priority);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'completed') {
                $query->where('is_completed', true);
            } elseif ($request->status === 'dismissed') {
                $query->where('is_dismissed', true);
            } elseif ($request->status === 'expired') {
                $query->where('expires_at', '<=', now());
            }
        }

        // Sort
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $suggestions = $query->paginate(20);

        return Inertia::render('Admin/DataManagement/SmartSuggestions/Index', [
            'suggestions' => $suggestions,
            'filters' => $request->only(['search', 'category', 'priority', 'status', 'sort', 'direction']),
        ]);
    }

    public function create()
    {
        $users = User::select('id', 'name', 'email')->orderBy('name')->get();
        
        return Inertia::render('Admin/DataManagement/SmartSuggestions/Create', [
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'suggestion_type' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'priority' => 'required|in:urgent,high,medium,low',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'data' => 'nullable|json',
            'action_type' => 'nullable|string|max:255',
            'action_url' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date',
            'relevance_score' => 'nullable|integer|min:0|max:100',
        ]);

        // Decode data JSON
        if (isset($validated['data'])) {
            $validated['data'] = json_decode($validated['data'], true);
        }

        SmartSuggestion::create($validated);

        return redirect()
            ->route('admin.data.smart-suggestions.index')
            ->with('success', 'Smart suggestion created successfully.');
    }

    public function edit(SmartSuggestion $smartSuggestion)
    {
        $smartSuggestion->load('user:id,name,email');
        $users = User::select('id', 'name', 'email')->orderBy('name')->get();
        
        return Inertia::render('Admin/DataManagement/SmartSuggestions/Edit', [
            'suggestion' => $smartSuggestion,
            'users' => $users,
        ]);
    }

    public function update(Request $request, SmartSuggestion $smartSuggestion)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'suggestion_type' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'priority' => 'required|in:urgent,high,medium,low',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'data' => 'nullable|json',
            'action_type' => 'nullable|string|max:255',
            'action_url' => 'nullable|string|max:255',
            'is_completed' => 'boolean',
            'is_dismissed' => 'boolean',
            'expires_at' => 'nullable|date',
            'relevance_score' => 'nullable|integer|min:0|max:100',
        ]);

        // Decode data JSON
        if (isset($validated['data'])) {
            $validated['data'] = json_decode($validated['data'], true);
        }

        // Handle completion timestamp
        if ($validated['is_completed'] && !$smartSuggestion->is_completed) {
            $validated['completed_at'] = now();
        } elseif (!$validated['is_completed']) {
            $validated['completed_at'] = null;
        }

        // Handle dismissal timestamp
        if ($validated['is_dismissed'] && !$smartSuggestion->is_dismissed) {
            $validated['dismissed_at'] = now();
        } elseif (!$validated['is_dismissed']) {
            $validated['dismissed_at'] = null;
        }

        $smartSuggestion->update($validated);

        return redirect()
            ->route('admin.data.smart-suggestions.index')
            ->with('success', 'Smart suggestion updated successfully.');
    }

    public function destroy(SmartSuggestion $smartSuggestion)
    {
        $smartSuggestion->delete();

        return redirect()
            ->route('admin.data.smart-suggestions.index')
            ->with('success', 'Smart suggestion deleted successfully.');
    }

    public function markCompleted(SmartSuggestion $smartSuggestion)
    {
        $smartSuggestion->markAsCompleted();

        return back()->with('success', 'Suggestion marked as completed.');
    }

    public function dismiss(SmartSuggestion $smartSuggestion)
    {
        $smartSuggestion->dismiss();

        return back()->with('success', 'Suggestion dismissed.');
    }

    // BulkUploadable trait implementation
    protected function getModelClass(): string
    {
        return SmartSuggestion::class;
    }

    protected function getRequiredColumns(): array
    {
        return ['user_id', 'suggestion_type', 'category', 'priority', 'title', 'description'];
    }

    protected function getOptionalColumns(): array
    {
        return ['data', 'action_type', 'action_url', 'expires_at', 'relevance_score'];
    }

    protected function getTemplateColumns(): array
    {
        return array_merge($this->getRequiredColumns(), $this->getOptionalColumns());
    }

    protected function getValidationRules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'suggestion_type' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'priority' => 'required|in:urgent,high,medium,low',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'data' => 'nullable|string',
            'action_type' => 'nullable|string|max:255',
            'action_url' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date',
            'relevance_score' => 'nullable|integer|min:0|max:100',
        ];
    }

    protected function saveRecord(array $data)
    {
        return SmartSuggestion::create($data);
    }

    protected function getColumnDescriptions(): array
    {
        return [
            'user_id' => 'User ID (must exist in users table)',
            'suggestion_type' => 'Type of suggestion (e.g., profile_completion, document_upload)',
            'category' => 'Category (visa, profile, document, application, assessment)',
            'priority' => 'Priority level (urgent, high, medium, low)',
            'title' => 'Suggestion title',
            'description' => 'Detailed description',
            'data' => 'Additional data as JSON (optional)',
            'action_type' => 'Action type (optional)',
            'action_url' => 'Action URL or route (optional)',
            'expires_at' => 'Expiration date (YYYY-MM-DD HH:MM:SS, optional)',
            'relevance_score' => 'Relevance score (0-100, default: 50)',
        ];
    }

    protected function getSampleData(): array
    {
        return [
            [
                'user_id' => '1',
                'suggestion_type' => 'profile_completion',
                'category' => 'profile',
                'priority' => 'high',
                'title' => 'Complete Your Profile',
                'description' => 'Your profile is 60% complete. Add your education and work experience to improve your chances.',
                'data' => '{"completion_percentage":60,"missing_fields":["education","work_experience"]}',
                'action_type' => 'navigate',
                'action_url' => '/profile/edit',
                'expires_at' => '',
                'relevance_score' => '85',
            ],
            [
                'user_id' => '1',
                'suggestion_type' => 'document_upload',
                'category' => 'document',
                'priority' => 'urgent',
                'title' => 'Upload Passport Copy',
                'description' => 'Your visa application requires a valid passport copy. Please upload it as soon as possible.',
                'data' => '{"document_type":"passport","application_id":123}',
                'action_type' => 'navigate',
                'action_url' => '/documents/upload',
                'expires_at' => '2025-12-31 23:59:59',
                'relevance_score' => '95',
            ],
            [
                'user_id' => '2',
                'suggestion_type' => 'skill_assessment',
                'category' => 'assessment',
                'priority' => 'medium',
                'title' => 'Take Language Proficiency Test',
                'description' => 'Improve your profile by taking a language proficiency test. This will help employers find you.',
                'data' => '{"test_type":"language","suggested_tests":["IELTS","TOEFL"]}',
                'action_type' => 'navigate',
                'action_url' => '/language-tests',
                'expires_at' => '',
                'relevance_score' => '70',
            ],
        ];
    }

    protected function validateRow(array $row, int $rowNumber): ?string
    {
        if (!User::where('id', $row['user_id'])->exists()) {
            return "Row {$rowNumber}: User with ID '{$row['user_id']}' does not exist";
        }

        if (!in_array($row['priority'], ['urgent', 'high', 'medium', 'low'])) {
            return "Row {$rowNumber}: Priority must be one of: urgent, high, medium, low";
        }

        if (isset($row['relevance_score']) && ($row['relevance_score'] < 0 || $row['relevance_score'] > 100)) {
            return "Row {$rowNumber}: Relevance score must be between 0 and 100";
        }

        return null;
    }

    protected function transformRowData(array $row): array
    {
        $data = [
            'user_id' => (int)$row['user_id'],
            'suggestion_type' => trim($row['suggestion_type']),
            'category' => trim($row['category']),
            'priority' => strtolower(trim($row['priority'])),
            'title' => trim($row['title']),
            'description' => trim($row['description']),
            'data' => !empty($row['data']) ? json_decode($row['data'], true) : null,
            'action_type' => !empty($row['action_type']) ? trim($row['action_type']) : null,
            'action_url' => !empty($row['action_url']) ? trim($row['action_url']) : null,
            'expires_at' => !empty($row['expires_at']) ? $row['expires_at'] : null,
            'relevance_score' => isset($row['relevance_score']) ? (int)$row['relevance_score'] : 50,
        ];

        return $data;
    }

    protected function getExportQuery(Request $request)
    {
        return SmartSuggestion::with('user:id,name,email')->orderBy('created_at', 'desc');
    }

    protected function getExportColumns(): array
    {
        return [
            'id', 'user_id', 'user_name', 'user_email', 'suggestion_type', 'category', 'priority',
            'title', 'description', 'data', 'action_type', 'action_url',
            'is_completed', 'completed_at', 'is_dismissed', 'dismissed_at',
            'expires_at', 'relevance_score', 'created_at'
        ];
    }

    protected function prepareExportRow($model): array
    {
        return [
            'id' => $model->id,
            'user_id' => $model->user_id,
            'user_name' => $model->user->name ?? '',
            'user_email' => $model->user->email ?? '',
            'suggestion_type' => $model->suggestion_type,
            'category' => $model->category,
            'priority' => $model->priority,
            'title' => $model->title,
            'description' => $model->description,
            'data' => !empty($model->data) ? json_encode($model->data) : '',
            'action_type' => $model->action_type ?? '',
            'action_url' => $model->action_url ?? '',
            'is_completed' => $model->is_completed ? '1' : '0',
            'completed_at' => $model->completed_at ? $model->completed_at->format('Y-m-d H:i:s') : '',
            'is_dismissed' => $model->is_dismissed ? '1' : '0',
            'dismissed_at' => $model->dismissed_at ? $model->dismissed_at->format('Y-m-d H:i:s') : '',
            'expires_at' => $model->expires_at ? $model->expires_at->format('Y-m-d H:i:s') : '',
            'relevance_score' => $model->relevance_score,
            'created_at' => $model->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
