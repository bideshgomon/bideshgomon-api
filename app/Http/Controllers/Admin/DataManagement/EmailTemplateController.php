<?php

namespace App\Http\Controllers\Admin\DataManagement;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Traits\BulkUploadable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class EmailTemplateController extends Controller
{
    use BulkUploadable;

    public function index(Request $request)
    {
        $query = EmailTemplate::query();

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('slug', 'LIKE', "%{$search}%")
                  ->orWhere('subject', 'LIKE', "%{$search}%")
                  ->orWhere('category', 'LIKE', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category') && $request->category !== '') {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }

        // Sort
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');

        if ($sortField === 'campaigns') {
            $query->withCount('campaigns')->orderBy('campaigns_count', $sortDirection);
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        $templates = $query->withCount('campaigns')->paginate(20);

        return Inertia::render('Admin/DataManagement/EmailTemplates/Index', [
            'templates' => $templates,
            'filters' => $request->only(['search', 'category', 'is_active', 'sort', 'direction']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/DataManagement/EmailTemplates/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:email_templates,name',
            'slug' => 'nullable|string|max:255|unique:email_templates,slug',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'html_body' => 'nullable|string',
            'category' => 'required|string|max:100',
            'variables' => 'nullable|json',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        if (isset($validated['variables'])) {
            $validated['variables'] = json_decode($validated['variables'], true);
        }

        EmailTemplate::create($validated);

        return redirect()
            ->route('admin.data.email-templates.index')
            ->with('success', 'Email template created successfully.');
    }

    public function edit(EmailTemplate $emailTemplate)
    {
        $emailTemplate->load('campaigns:id,name');
        
        return Inertia::render('Admin/DataManagement/EmailTemplates/Edit', [
            'template' => $emailTemplate->append('campaigns_count'),
        ]);
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:email_templates,name,' . $emailTemplate->id,
            'slug' => 'nullable|string|max:255|unique:email_templates,slug,' . $emailTemplate->id,
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'html_body' => 'nullable|string',
            'category' => 'required|string|max:100',
            'variables' => 'nullable|json',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        if (isset($validated['variables'])) {
            $validated['variables'] = json_decode($validated['variables'], true);
        }

        $emailTemplate->update($validated);

        return redirect()
            ->route('admin.data.email-templates.index')
            ->with('success', 'Email template updated successfully.');
    }

    public function destroy(EmailTemplate $emailTemplate)
    {
        // Check if template has campaigns
        if ($emailTemplate->campaigns()->exists()) {
            return back()->with('error', 'Cannot delete template that is used in campaigns.');
        }

        $emailTemplate->delete();

        return redirect()
            ->route('admin.data.email-templates.index')
            ->with('success', 'Email template deleted successfully.');
    }

    public function toggleStatus(EmailTemplate $emailTemplate)
    {
        $emailTemplate->is_active = !$emailTemplate->is_active;
        $emailTemplate->save();

        return back();
    }

    // BulkUploadable trait implementation
    protected function getModelClass(): string
    {
        return EmailTemplate::class;
    }

    protected function getRequiredColumns(): array
    {
        return ['name', 'subject', 'body', 'category'];
    }

    protected function getOptionalColumns(): array
    {
        return ['slug', 'html_body', 'variables', 'is_active'];
    }

    protected function getTemplateColumns(): array
    {
        return array_merge($this->getRequiredColumns(), $this->getOptionalColumns());
    }

    protected function getValidationRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'html_body' => 'nullable|string',
            'category' => 'required|string|max:100',
            'variables' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }

    protected function saveRecord(array $data)
    {
        return EmailTemplate::create($data);
    }

    protected function getColumnDescriptions(): array
    {
        return [
            'name' => 'Template name (must be unique)',
            'slug' => 'URL-friendly identifier (auto-generated if empty)',
            'subject' => 'Email subject line',
            'body' => 'Plain text email body',
            'html_body' => 'HTML email body (optional)',
            'category' => 'Category (e.g., general, transactional, marketing)',
            'variables' => 'Available variables as JSON array (optional)',
            'is_active' => '1 for active, 0 for inactive (default: 1)',
        ];
    }

    protected function getSampleData(): array
    {
        return [
            [
                'name' => 'Welcome Email',
                'slug' => 'welcome-email',
                'subject' => 'Welcome to Bideshgomon',
                'body' => 'Dear {{name}}, welcome to Bideshgomon. We are here to help you with all your visa and travel needs.',
                'html_body' => '<h1>Welcome</h1><p>Dear {{name}}, welcome to Bideshgomon.</p>',
                'category' => 'transactional',
                'variables' => '["name", "email"]',
                'is_active' => '1',
            ],
            [
                'name' => 'Password Reset',
                'slug' => 'password-reset',
                'subject' => 'Reset Your Password',
                'body' => 'Hi {{name}}, click the link to reset your password: {{reset_link}}',
                'html_body' => '<p>Hi {{name}}, <a href="{{reset_link}}">click here</a> to reset your password.</p>',
                'category' => 'transactional',
                'variables' => '["name", "reset_link"]',
                'is_active' => '1',
            ],
            [
                'name' => 'Newsletter',
                'slug' => 'newsletter',
                'subject' => 'Monthly Newsletter - {{month}}',
                'body' => 'Check out our latest visa guides and travel tips for {{month}}.',
                'html_body' => '<h2>Newsletter - {{month}}</h2><p>Check out our latest content.</p>',
                'category' => 'marketing',
                'variables' => '["month", "content"]',
                'is_active' => '1',
            ],
        ];
    }

    protected function validateRow(array $row, int $rowNumber): ?string
    {
        if (EmailTemplate::where('name', $row['name'])->exists()) {
            return "Row {$rowNumber}: Template with name '{$row['name']}' already exists";
        }

        if (!empty($row['slug']) && EmailTemplate::where('slug', $row['slug'])->exists()) {
            return "Row {$rowNumber}: Template with slug '{$row['slug']}' already exists";
        }

        return null;
    }

    protected function transformRowData(array $row): array
    {
        $data = [
            'name' => trim($row['name']),
            'slug' => !empty($row['slug']) ? Str::slug(trim($row['slug'])) : Str::slug(trim($row['name'])),
            'subject' => trim($row['subject']),
            'body' => trim($row['body']),
            'html_body' => !empty($row['html_body']) ? trim($row['html_body']) : null,
            'category' => trim($row['category']),
            'variables' => !empty($row['variables']) ? json_decode($row['variables'], true) : null,
            'is_active' => isset($row['is_active']) ? (bool)$row['is_active'] : true,
        ];

        return $data;
    }

    protected function getExportQuery(Request $request)
    {
        return EmailTemplate::query()->orderBy('category')->orderBy('name');
    }

    protected function getExportColumns(): array
    {
        return ['name', 'slug', 'subject', 'body', 'html_body', 'category', 'variables', 'is_active'];
    }

    protected function prepareExportRow($model): array
    {
        return [
            'name' => $model->name,
            'slug' => $model->slug,
            'subject' => $model->subject,
            'body' => $model->body,
            'html_body' => $model->html_body ?? '',
            'category' => $model->category,
            'variables' => !empty($model->variables) ? json_encode($model->variables) : '',
            'is_active' => $model->is_active ? '1' : '0',
        ];
    }
}
