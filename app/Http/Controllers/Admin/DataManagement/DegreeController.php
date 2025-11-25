<?php

namespace App\Http\Controllers\Admin\DataManagement;

use App\Http\Controllers\Controller;
use App\Models\Degree;
use App\Traits\BulkUploadable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class DegreeController extends Controller
{
    use BulkUploadable;

    public function index(Request $request)
    {
        $query = Degree::query();

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('name_bn', 'LIKE', "%{$search}%")
                  ->orWhere('short_name', 'LIKE', "%{$search}%")
                  ->orWhere('level', 'LIKE', "%{$search}%");
            });
        }

        // Filter by level
        if ($request->has('level')) {
            $query->where('level', $request->level);
        }

        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Sort
        $sortField = $request->get('sort_field', 'level');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        if ($sortField === 'level') {
            $query->orderBy('level', $sortDirection)->orderBy('name');
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        // Paginate
        $perPage = $request->get('per_page', 15);
        $degrees = $query->paginate($perPage);

        return Inertia::render('Admin/DataManagement/Degrees/Index', [
            'degrees' => $degrees,
            'filters' => [
                'search' => $request->search,
                'level' => $request->level,
                'is_active' => $request->is_active,
            ],
            'sort' => [
                'field' => $sortField,
                'direction' => $sortDirection,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/DataManagement/Degrees/Create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:degrees,name',
            'name_bn' => 'nullable|string|max:255',
            'short_name' => 'required|string|max:50',
            'level' => 'required|string|max:50',
            'typical_duration_years' => 'nullable|integer|min:1|max:10',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $degree = Degree::create($validator->validated());

        return redirect()->route('admin.data.degrees.index')
            ->with('success', 'Degree created successfully');
    }

    public function edit($id)
    {
        $degree = Degree::findOrFail($id);

        return Inertia::render('Admin/DataManagement/Degrees/Edit', [
            'degree' => $degree,
        ]);
    }

    public function update(Request $request, $id)
    {
        $degree = Degree::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:degrees,name,' . $id,
            'name_bn' => 'nullable|string|max:255',
            'short_name' => 'required|string|max:50',
            'level' => 'required|string|max:50',
            'typical_duration_years' => 'nullable|integer|min:1|max:10',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $degree->update($validator->validated());

        return redirect()->route('admin.data.degrees.index')
            ->with('success', 'Degree updated successfully');
    }

    public function destroy($id)
    {
        $degree = Degree::findOrFail($id);
        $degree->delete();

        return redirect()->route('admin.data.degrees.index')
            ->with('success', 'Degree deleted successfully');
    }

    public function toggleStatus($id)
    {
        $degree = Degree::findOrFail($id);
        $degree->is_active = !$degree->is_active;
        $degree->save();

        return back()->with('success', 'Degree status updated successfully');
    }

    // BulkUploadable implementation
    protected function getModelClass(): string
    {
        return Degree::class;
    }

    protected function getRequiredColumns(): array
    {
        return ['name', 'short_name', 'level'];
    }

    protected function getOptionalColumns(): array
    {
        return ['name_bn', 'typical_duration_years', 'is_active'];
    }

    protected function getTemplateColumns(): array
    {
        return array_merge($this->getRequiredColumns(), $this->getOptionalColumns());
    }

    protected function getValidationRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'short_name' => 'required|string|max:50',
            'level' => 'required|string|max:100',
            'typical_duration_years' => 'nullable|integer|min:1|max:10',
            'is_active' => 'nullable|boolean',
        ];
    }

    protected function saveRecord(array $data)
    {
        return Degree::create($data);
    }

    protected function getColumnDescriptions(): array
    {
        return [
            'name' => 'Degree name in English (must be unique)',
            'name_bn' => 'Degree name in Bengali (optional)',
            'short_name' => 'Short name/abbreviation (e.g., BSc, MSc, PhD)',
            'level' => 'Education level (e.g., Undergraduate, Graduate, Postgraduate)',
            'typical_duration_years' => 'Typical duration in years (optional)',
            'is_active' => 'Is active? (1 or 0, default: 1)',
        ];
    }

    protected function getSampleData(): array
    {
        return [
            [
                'name' => 'Bachelor of Science',
                'name_bn' => 'বিজ্ঞান স্নাতক',
                'short_name' => 'BSc',
                'level' => 'Undergraduate',
                'typical_duration_years' => '4',
                'is_active' => '1',
            ],
            [
                'name' => 'Master of Science',
                'name_bn' => 'বিজ্ঞান স্নাতকোত্তর',
                'short_name' => 'MSc',
                'level' => 'Graduate',
                'typical_duration_years' => '2',
                'is_active' => '1',
            ],
            [
                'name' => 'Doctor of Philosophy',
                'name_bn' => 'পিএইচডি',
                'short_name' => 'PhD',
                'level' => 'Postgraduate',
                'typical_duration_years' => '5',
                'is_active' => '1',
            ],
        ];
    }

    protected function validateRow(array $row, int $rowNumber): ?string
    {
        // Check if name is unique
        if (Degree::where('name', $row['name'])->exists()) {
            return "Row {$rowNumber}: Degree with name '{$row['name']}' already exists";
        }

        // Validate typical_duration_years if provided
        if (isset($row['typical_duration_years']) && !empty($row['typical_duration_years'])) {
            if (!is_numeric($row['typical_duration_years']) || $row['typical_duration_years'] < 1 || $row['typical_duration_years'] > 10) {
                return "Row {$rowNumber}: typical_duration_years must be a number between 1 and 10";
            }
        }

        return null;
    }

    protected function transformRowData(array $row): array
    {
        return [
            'name' => $row['name'],
            'name_bn' => $row['name_bn'] ?? null,
            'short_name' => $row['short_name'],
            'level' => $row['level'],
            'typical_duration_years' => isset($row['typical_duration_years']) && $row['typical_duration_years'] !== '' ? (int)$row['typical_duration_years'] : null,
            'is_active' => isset($row['is_active']) ? (bool)$row['is_active'] : true,
        ];
    }

    protected function getExportQuery(Request $request)
    {
        return Degree::active()->ordered();
    }

    protected function getExportColumns(): array
    {
        return [
            'name',
            'name_bn',
            'short_name',
            'level',
            'typical_duration_years',
            'is_active',
        ];
    }

    protected function prepareExportRow($degree): array
    {
        return [
            'name' => $degree->name,
            'name_bn' => $degree->name_bn ?? '',
            'short_name' => $degree->short_name,
            'level' => $degree->level,
            'typical_duration_years' => $degree->typical_duration_years ?? '',
            'is_active' => $degree->is_active ? '1' : '0',
        ];
    }
}
