<?php

namespace App\Http\Controllers\Admin\DataManagement;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Traits\BulkUploadable;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CountryController extends Controller
{
    use BulkUploadable;

    protected $entityName = 'Country';
    protected $entityNamePlural = 'Countries';
    protected $indexRoute = 'admin.data.countries.index';
    protected $bulkUploadView = 'Admin/DataManagement/Countries/BulkUpload';

    /**
     * Display a listing of countries
     */
    public function index(Request $request)
    {
        $query = Country::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_bn', 'like', "%{$search}%")
                  ->orWhere('iso_code_2', 'like', "%{$search}%")
                  ->orWhere('iso_code_3', 'like', "%{$search}%");
            });
        }

        // Filter by region
        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'true');
        }

        // Sort
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $countries = $query->paginate($request->get('per_page', 15))
            ->withQueryString();

        // Get unique regions for filter
        $regions = Country::select('region')
            ->whereNotNull('region')
            ->distinct()
            ->pluck('region')
            ->sort()
            ->values();

        return Inertia::render('Admin/DataManagement/Countries/Index', [
            'countries' => $countries,
            'regions' => $regions,
            'filters' => $request->only(['search', 'region', 'is_active']),
            'sort' => [
                'field' => $sortField,
                'direction' => $sortDirection,
            ],
        ]);
    }

    /**
     * Show the form for creating a new country
     */
    public function create()
    {
        return Inertia::render('Admin/DataManagement/Countries/Create');
    }

    /**
     * Store a newly created country
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:countries,name',
            'name_bn' => 'nullable|string|max:100',
            'iso_code_2' => 'required|string|size:2|unique:countries,iso_code_2',
            'iso_code_3' => 'required|string|size:3|unique:countries,iso_code_3',
            'phone_code' => 'required|string|max:10',
            'currency_code' => 'required|string|size:3',
            'flag_emoji' => 'nullable|string|max:10',
            'region' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        try {
            Country::create($validated);

            return redirect()->route('admin.data.countries.index')
                ->with('success', 'Country created successfully.');
        } catch (\Exception $e) {
            Log::error('Country creation failed', [
                'error' => $e->getMessage(),
                'data' => $validated,
            ]);

            return back()->withInput()->with('error', 'Failed to create country: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified country
     */
    public function show(Country $country)
    {
        return Inertia::render('Admin/DataManagement/Countries/Show', [
            'country' => $country,
        ]);
    }

    /**
     * Show the form for editing the specified country
     */
    public function edit(Country $country)
    {
        return Inertia::render('Admin/DataManagement/Countries/Edit', [
            'country' => $country,
        ]);
    }

    /**
     * Update the specified country
     */
    public function update(Request $request, Country $country)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('countries')->ignore($country->id)],
            'name_bn' => 'nullable|string|max:100',
            'iso_code_2' => ['required', 'string', 'size:2', Rule::unique('countries')->ignore($country->id)],
            'iso_code_3' => ['required', 'string', 'size:3', Rule::unique('countries')->ignore($country->id)],
            'phone_code' => 'required|string|max:10',
            'currency_code' => 'required|string|size:3',
            'flag_emoji' => 'nullable|string|max:10',
            'region' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        try {
            $country->update($validated);

            return redirect()->route('admin.data.countries.index')
                ->with('success', 'Country updated successfully.');
        } catch (\Exception $e) {
            Log::error('Country update failed', [
                'country_id' => $country->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withInput()->with('error', 'Failed to update country: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified country
     */
    public function destroy(Country $country)
    {
        try {
            $country->delete();

            return redirect()->route('admin.data.countries.index')
                ->with('success', 'Country deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Country deletion failed', [
                'country_id' => $country->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to delete country: ' . $e->getMessage());
        }
    }

    /**
     * Toggle country status
     */
    public function toggleStatus(Country $country)
    {
        try {
            $country->update(['is_active' => !$country->is_active]);

            return back()->with('success', 'Country status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Country status toggle failed', [
                'country_id' => $country->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to update country status.');
        }
    }

    // BulkUploadable trait implementations

    protected function getTemplateColumns(): array
    {
        return [
            'name',
            'name_bn',
            'iso_code_2',
            'iso_code_3',
            'phone_code',
            'currency_code',
            'flag_emoji',
            'region',
            'is_active'
        ];
    }

    protected function getRequiredColumns(): array
    {
        return [
            'name',
            'iso_code_2',
            'iso_code_3',
            'phone_code',
            'currency_code'
        ];
    }

    protected function getValidationRules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'name_bn' => 'nullable|string|max:100',
            'iso_code_2' => 'required|string|size:2',
            'iso_code_3' => 'required|string|size:3',
            'phone_code' => 'required|string|max:10',
            'currency_code' => 'required|string|size:3',
            'flag_emoji' => 'nullable|string|max:10',
            'region' => 'nullable|string|max:50',
            'is_active' => 'nullable|in:true,false,1,0,yes,no',
        ];
    }

    protected function getSampleData(): array
    {
        return [
            ['Bangladesh', 'বাংলাদেশ', 'BD', 'BGD', '+880', 'BDT', '🇧🇩', 'South Asia', 'true'],
            ['United States', 'যুক্তরাষ্ট্র', 'US', 'USA', '+1', 'USD', '🇺🇸', 'North America', 'true'],
            ['United Kingdom', 'যুক্তরাজ্য', 'GB', 'GBR', '+44', 'GBP', '🇬🇧', 'Europe', 'true'],
        ];
    }

    protected function transformRowData(array $data): array
    {
        // Convert is_active to boolean
        if (isset($data['is_active'])) {
            $data['is_active'] = in_array(strtolower($data['is_active']), ['true', '1', 'yes']);
        }

        // Uppercase ISO codes
        if (isset($data['iso_code_2'])) {
            $data['iso_code_2'] = strtoupper($data['iso_code_2']);
        }
        if (isset($data['iso_code_3'])) {
            $data['iso_code_3'] = strtoupper($data['iso_code_3']);
        }
        if (isset($data['currency_code'])) {
            $data['currency_code'] = strtoupper($data['currency_code']);
        }

        return $data;
    }

    protected function saveRecord(array $data)
    {
        Country::updateOrCreate(
            ['iso_code_2' => $data['iso_code_2']],
            $data
        );
    }

    protected function getExportQuery(Request $request)
    {
        $query = Country::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_bn', 'like', "%{$search}%")
                  ->orWhere('iso_code_2', 'like', "%{$search}%");
            });
        }

        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'true');
        }

        return $query->orderBy('name');
    }

    protected function getExportColumns(): array
    {
        return $this->getTemplateColumns();
    }

    protected function prepareExportRow($record): array
    {
        return [
            $record->name,
            $record->name_bn,
            $record->iso_code_2,
            $record->iso_code_3,
            $record->phone_code,
            $record->currency_code,
            $record->flag_emoji,
            $record->region,
            $record->is_active ? 'true' : 'false',
        ];
    }
}
