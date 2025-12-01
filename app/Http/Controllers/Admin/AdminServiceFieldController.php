<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceModule;
use App\Models\ServiceFormField;
use App\Services\DataMapperService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminServiceFieldController extends Controller
{
    protected $dataMapper;

    public function __construct(DataMapperService $dataMapper)
    {
        $this->dataMapper = $dataMapper;
    }

    /**
     * Display form fields for a service
     */
    public function index(ServiceModule $service): Response
    {
        $fields = $service->formFields()
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group_name');

        $availableProfileFields = $this->dataMapper->getAvailableProfileFields();

        return Inertia::render('Admin/Services/Fields/Index', [
            'service' => $service->load('category'),
            'fields' => $fields,
            'availableProfileFields' => $availableProfileFields,
            'fieldTypes' => ServiceFormField::FIELD_TYPES,
        ]);
    }

    /**
     * Show the form for creating a new field
     */
    public function create(ServiceModule $service): Response
    {
        $availableProfileFields = $this->dataMapper->getAvailableProfileFields();

        return Inertia::render('Admin/Services/Fields/Create', [
            'service' => $service,
            'availableProfileFields' => $availableProfileFields,
            'fieldTypes' => ServiceFormField::FIELD_TYPES,
        ]);
    }

    /**
     * Store a newly created field
     */
    public function store(Request $request, ServiceModule $service)
    {
        $validated = $request->validate([
            'field_name' => 'required|string|max:255',
            'field_label' => 'required|string|max:255',
            'field_type' => 'required|string|in:' . implode(',', array_keys(ServiceFormField::FIELD_TYPES)),
            'placeholder' => 'nullable|string',
            'help_text' => 'nullable|string',
            'default_value' => 'nullable|string',
            'is_required' => 'boolean',
            'is_readonly' => 'boolean',
            'validation_rules' => 'nullable|string',
            'min_length' => 'nullable|integer|min:0',
            'max_length' => 'nullable|integer|min:0',
            'profile_map_key' => 'nullable|string',
            'profile_map_table' => 'nullable|string',
            'profile_map_column' => 'nullable|string',
            'options' => 'nullable|array',
            'allow_multiple' => 'boolean',
            'accepted_file_types' => 'nullable|string',
            'max_file_size' => 'nullable|integer|min:0',
            'conditional_rules' => 'nullable|array',
            'group_name' => 'nullable|string|max:255',
            'section_name' => 'nullable|string|max:255',
            'column_width' => 'nullable|integer|min:1|max:12',
            'css_class' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        // Auto-generate field_name from label if not provided
        if (empty($validated['field_name'])) {
            $validated['field_name'] = \Str::snake($validated['field_label']);
        }

        // Set default sort_order if not provided
        if (!isset($validated['sort_order'])) {
            $maxOrder = $service->formFields()->max('sort_order') ?? 0;
            $validated['sort_order'] = $maxOrder + 1;
        }

        // Set service_module_id
        $validated['service_module_id'] = $service->id;

        $field = ServiceFormField::create($validated);

        return redirect()
            ->route('admin.services.fields.index', $service)
            ->with('success', 'Form field added successfully.');
    }

    /**
     * Show the form for editing the specified field
     */
    public function edit(ServiceModule $service, ServiceFormField $field): Response
    {
        $availableProfileFields = $this->dataMapper->getAvailableProfileFields();

        return Inertia::render('Admin/Services/Fields/Edit', [
            'service' => $service,
            'field' => $field,
            'availableProfileFields' => $availableProfileFields,
            'fieldTypes' => ServiceFormField::FIELD_TYPES,
        ]);
    }

    /**
     * Update the specified field
     */
    public function update(Request $request, ServiceModule $service, ServiceFormField $field)
    {
        $validated = $request->validate([
            'field_name' => 'required|string|max:255',
            'field_label' => 'required|string|max:255',
            'field_type' => 'required|string|in:' . implode(',', array_keys(ServiceFormField::FIELD_TYPES)),
            'placeholder' => 'nullable|string',
            'help_text' => 'nullable|string',
            'default_value' => 'nullable|string',
            'is_required' => 'boolean',
            'is_readonly' => 'boolean',
            'is_active' => 'boolean',
            'validation_rules' => 'nullable|string',
            'min_length' => 'nullable|integer|min:0',
            'max_length' => 'nullable|integer|min:0',
            'profile_map_key' => 'nullable|string',
            'profile_map_table' => 'nullable|string',
            'profile_map_column' => 'nullable|string',
            'options' => 'nullable|array',
            'allow_multiple' => 'boolean',
            'accepted_file_types' => 'nullable|string',
            'max_file_size' => 'nullable|integer|min:0',
            'conditional_rules' => 'nullable|array',
            'group_name' => 'nullable|string|max:255',
            'section_name' => 'nullable|string|max:255',
            'column_width' => 'nullable|integer|min:1|max:12',
            'css_class' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        $field->update($validated);

        return back()->with('success', 'Form field updated successfully.');
    }

    /**
     * Remove the specified field
     */
    public function destroy(ServiceModule $service, ServiceFormField $field)
    {
        $field->delete();

        return back()->with('success', 'Form field deleted successfully.');
    }

    /**
     * Reorder fields (drag and drop)
     */
    public function reorder(Request $request, ServiceModule $service)
    {
        $validated = $request->validate([
            'fields' => 'required|array',
            'fields.*.id' => 'required|exists:service_form_fields,id',
            'fields.*.sort_order' => 'required|integer',
            'fields.*.group_name' => 'nullable|string',
        ]);

        foreach ($validated['fields'] as $fieldData) {
            ServiceFormField::where('id', $fieldData['id'])
                ->where('service_module_id', $service->id)
                ->update([
                    'sort_order' => $fieldData['sort_order'],
                    'group_name' => $fieldData['group_name'] ?? null,
                ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Fields reordered successfully.',
        ]);
    }

    /**
     * Duplicate a field
     */
    public function duplicate(ServiceModule $service, ServiceFormField $field)
    {
        $newField = $field->replicate();
        $newField->field_name = $field->field_name . '_copy';
        $newField->field_label = $field->field_label . ' (Copy)';
        $newField->sort_order = $service->formFields()->max('sort_order') + 1;
        $newField->save();

        return back()->with('success', 'Field duplicated successfully.');
    }

    /**
     * Toggle field status
     */
    public function toggleStatus(ServiceModule $service, ServiceFormField $field)
    {
        $field->update([
            'is_active' => !$field->is_active,
        ]);

        return back()->with('success', 'Field status updated successfully.');
    }

    /**
     * Get available profile fields for mapping (AJAX)
     */
    public function getProfileFields()
    {
        return response()->json([
            'profileFields' => $this->dataMapper->getAvailableProfileFields(),
        ]);
    }

    /**
     * Preview form structure
     */
    public function preview(ServiceModule $service)
    {
        $fields = $service->formFields()
            ->active()
            ->ordered()
            ->get()
            ->groupBy('group_name');

        return response()->json([
            'service' => $service->only(['id', 'name', 'description']),
            'fields' => $fields,
            'groups' => $fields->keys(),
        ]);
    }

    /**
     * Validate field configuration
     */
    public function validateField(Request $request)
    {
        $validated = $request->validate([
            'field_name' => 'required|string|max:255',
            'field_type' => 'required|string',
            'validation_rules' => 'nullable|string',
            'profile_map_key' => 'nullable|string',
        ]);

        $errors = [];

        // Check if field_name is valid PHP variable name
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $validated['field_name'])) {
            $errors['field_name'] = 'Field name must be a valid variable name (letters, numbers, underscores only)';
        }

        // Validate Laravel validation rules
        if (!empty($validated['validation_rules'])) {
            try {
                validator(['test' => 'value'], ['test' => $validated['validation_rules']]);
            } catch (\Exception $e) {
                $errors['validation_rules'] = 'Invalid validation rules format';
            }
        }

        // Check if profile map key exists (basic check)
        if (!empty($validated['profile_map_key'])) {
            $availableKeys = collect($this->dataMapper->getAvailableProfileFields())
                ->flatten(1)
                ->pluck('key')
                ->toArray();
            
            if (!in_array($validated['profile_map_key'], $availableKeys)) {
                $errors['profile_map_key'] = 'Profile field mapping not found in available fields';
            }
        }

        return response()->json([
            'valid' => empty($errors),
            'errors' => $errors,
        ]);
    }

    /**
     * Bulk update fields
     */
    public function bulkUpdate(Request $request, ServiceModule $service)
    {
        $validated = $request->validate([
            'action' => 'required|in:activate,deactivate,delete,change_group',
            'field_ids' => 'required|array',
            'field_ids.*' => 'exists:service_form_fields,id',
            'group_name' => 'required_if:action,change_group|string|nullable',
        ]);

        $fields = ServiceFormField::whereIn('id', $validated['field_ids'])
            ->where('service_module_id', $service->id);

        switch ($validated['action']) {
            case 'activate':
                $fields->update(['is_active' => true]);
                $message = 'Fields activated successfully.';
                break;
            case 'deactivate':
                $fields->update(['is_active' => false]);
                $message = 'Fields deactivated successfully.';
                break;
            case 'delete':
                $fields->delete();
                $message = 'Fields deleted successfully.';
                break;
            case 'change_group':
                $fields->update(['group_name' => $validated['group_name']]);
                $message = 'Fields moved to new group successfully.';
                break;
        }

        return back()->with('success', $message);
    }
}
