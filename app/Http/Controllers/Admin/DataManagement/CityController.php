<?php

namespace App\Http\Controllers\Admin\DataManagement;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Traits\BulkUploadable;
use App\Models\City;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CityController extends Controller
{
    use BulkUploadable;

    protected $entityName = 'City';
    protected $entityNamePlural = 'Cities';
    protected $indexRoute = 'admin.data.cities.index';
    protected $bulkUploadView = 'Admin/DataManagement/Cities/BulkUpload';

    /**
     * Display a listing of cities
     */
    public function index(Request $request)
    {
        $query = City::with('country');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_bn', 'like', "%{$search}%")
                  ->orWhere('state_province', 'like', "%{$search}%");
            });
        }

        // Filter by country
        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'true');
        }

        // Sort
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $cities = $query->paginate($request->get('per_page', 15))
            ->withQueryString();

        // Get all active countries for filter dropdown
        $countries = Country::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/DataManagement/Cities/Index', [
            'cities' => $cities,
            'countries' => $countries,
            'filters' => $request->only(['search', 'country_id', 'is_active']),
            'sort' => [
                'field' => $sortField,
                'direction' => $sortDirection,
            ],
        ]);
    }

    /**
     * Show the form for creating a new city
     */
    public function create()
    {
        $countries = Country::where('is_active', true)->orderBy('name')->get();
        
        return Inertia::render('Admin/DataManagement/Cities/Create', [
            'countries' => $countries,
        ]);
    }

    /**
     * Store a newly created city
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|string|max:100|unique:cities,name',
            'name_bn' => 'nullable|string|max:100',
            'state_province' => 'nullable|string|max:100',
            'timezone' => 'nullable|string|max:50',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_capital' => 'boolean',
            'is_active' => 'boolean',
        ]);

        try {
            City::create($validated);

            return redirect()
                ->route('admin.data.cities.index')
                ->with('success', 'City created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating city: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create city.');
        }
    }

    /**
     * Show the form for editing a city
     */
    public function edit(City $city)
    {
        $countries = Country::where('is_active', true)->orderBy('name')->get();
        
        return Inertia::render('Admin/DataManagement/Cities/Edit', [
            'city' => $city->load('country'),
            'countries' => $countries,
        ]);
    }

    /**
     * Update the specified city
     */
    public function update(Request $request, City $city)
    {
        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'name' => ['required', 'string', 'max:100', Rule::unique('cities')->ignore($city->id)],
            'name_bn' => 'nullable|string|max:100',
            'state_province' => 'nullable|string|max:100',
            'timezone' => 'nullable|string|max:50',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_capital' => 'boolean',
            'is_active' => 'boolean',
        ]);

        try {
            $city->update($validated);

            return redirect()
                ->route('admin.data.cities.index')
                ->with('success', 'City updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating city: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update city.');
        }
    }

    /**
     * Remove the specified city
     */
    public function destroy(City $city)
    {
        try {
            $city->delete();

            return redirect()
                ->route('admin.data.cities.index')
                ->with('success', 'City deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting city: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete city.');
        }
    }

    /**
     * Toggle city status
     */
    public function toggleStatus(City $city)
    {
        try {
            $city->update([
                'is_active' => !$city->is_active,
            ]);

            return back()->with('success', 'City status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error toggling city status: ' . $e->getMessage());
            return back()->with('error', 'Failed to update city status.');
        }
    }

    // Bulk Upload Methods
    protected function getModelClass(): string
    {
        return City::class;
    }

    protected function getRequiredColumns(): array
    {
        return ['country_code', 'name'];
    }

    protected function getOptionalColumns(): array
    {
        return ['name_bn', 'state_province', 'timezone', 'latitude', 'longitude', 'is_capital', 'is_active'];
    }

    protected function getTemplateColumns(): array
    {
        return array_merge($this->getRequiredColumns(), $this->getOptionalColumns());
    }

    protected function getValidationRules(): array
    {
        return [
            'country_code' => 'required|string|size:2',
            'name' => 'required|string|max:100',
            'name_bn' => 'nullable|string|max:100',
            'state_province' => 'nullable|string|max:100',
            'timezone' => 'nullable|string|max:50',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_capital' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ];
    }

    protected function getColumnDescriptions(): array
    {
        return [
            'country_code' => 'Country ISO 2-letter code (e.g., BD, AE, GB)',
            'name' => 'City name in English',
            'name_bn' => 'City name in Bengali (optional)',
            'state_province' => 'State or province name (optional)',
            'timezone' => 'Timezone (e.g., Asia/Dhaka) (optional)',
            'latitude' => 'Latitude coordinate (optional)',
            'longitude' => 'Longitude coordinate (optional)',
            'is_capital' => 'Is capital city? (1 or 0, default: 0)',
            'is_active' => 'Is active? (1 or 0, default: 1)',
        ];
    }

    protected function getSampleData(): array
    {
        return [
            [
                'country_code' => 'BD',
                'name' => 'Dhaka',
                'name_bn' => 'ঢাকা',
                'state_province' => 'Dhaka Division',
                'timezone' => 'Asia/Dhaka',
                'latitude' => '23.8103',
                'longitude' => '90.4125',
                'is_capital' => '1',
                'is_active' => '1',
            ],
            [
                'country_code' => 'AE',
                'name' => 'Dubai',
                'name_bn' => 'দুবাই',
                'state_province' => 'Dubai',
                'timezone' => 'Asia/Dubai',
                'latitude' => '25.2048',
                'longitude' => '55.2708',
                'is_capital' => '0',
                'is_active' => '1',
            ],
        ];
    }

    protected function validateRow(array $row, int $rowNumber): ?string
    {
        // Check if country exists
        $country = Country::where('iso_code_2', $row['country_code'])->first();
        if (!$country) {
            return "Row {$rowNumber}: Country with code '{$row['country_code']}' not found";
        }

        return null;
    }

    protected function transformRowData(array $row): array
    {
        // Convert country_code to country_id
        $country = Country::where('iso_code_2', $row['country_code'])->first();

        return [
            'country_id' => $country->id,
            'name' => $row['name'],
            'name_bn' => $row['name_bn'] ?? null,
            'state_province' => $row['state_province'] ?? null,
            'timezone' => $row['timezone'] ?? null,
            'latitude' => isset($row['latitude']) && $row['latitude'] !== '' ? $row['latitude'] : null,
            'longitude' => isset($row['longitude']) && $row['longitude'] !== '' ? $row['longitude'] : null,
            'is_capital' => isset($row['is_capital']) ? (bool)$row['is_capital'] : false,
            'is_active' => isset($row['is_active']) ? (bool)$row['is_active'] : true,
        ];
    }

    protected function saveRecord(array $data)
    {
        return City::create($data);
    }

    protected function getExportColumns(): array
    {
        return [
            'country_code',
            'name',
            'name_bn',
            'state_province',
            'timezone',
            'latitude',
            'longitude',
            'is_capital',
            'is_active',
        ];
    }

    protected function getExportQuery(Request $request)
    {
        $query = City::with('country');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_bn', 'like', "%{$search}%");
            });
        }

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'true');
        }

        return $query;
    }

    protected function prepareExportRow($city): array
    {
        return [
            'country_code' => $city->country->iso_code_2 ?? '',
            'name' => $city->name,
            'name_bn' => $city->name_bn ?? '',
            'state_province' => $city->state_province ?? '',
            'timezone' => $city->timezone ?? '',
            'latitude' => $city->latitude ?? '',
            'longitude' => $city->longitude ?? '',
            'is_capital' => $city->is_capital ? '1' : '0',
            'is_active' => $city->is_active ? '1' : '0',
        ];
    }
}
