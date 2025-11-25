<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Traits\BulkUploadable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CityController extends Controller
{
    use BulkUploadable;

    public function index(Request $request)
    {
        $query = City::with('country');

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('name_bn', 'LIKE', "%{$search}%")
                  ->orWhere('state_province', 'LIKE', "%{$search}%");
            });
        }

        // Filter by country
        if ($request->has('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Filter by capital
        if ($request->has('is_capital')) {
            $query->where('is_capital', $request->is_capital);
        }

        // Sort
        $sortField = $request->get('sort_field', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Paginate
        $perPage = $request->get('per_page', 15);
        $cities = $query->paginate($perPage);

        return response()->json($cities);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'state_province' => 'nullable|string|max:255',
            'timezone' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_capital' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $city = City::create($validator->validated());
        $city->load('country');

        return response()->json([
            'message' => 'City created successfully',
            'data' => $city
        ], 201);
    }

    public function show($id)
    {
        $city = City::with('country')->findOrFail($id);
        return response()->json($city);
    }

    public function update(Request $request, $id)
    {
        $city = City::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'country_id' => 'sometimes|required|exists:countries,id',
            'name' => 'sometimes|required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'state_province' => 'nullable|string|max:255',
            'timezone' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_capital' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $city->update($validator->validated());
        $city->load('country');

        return response()->json([
            'message' => 'City updated successfully',
            'data' => $city
        ]);
    }

    public function destroy($id)
    {
        $city = City::findOrFail($id);
        
        // Check if city has airports
        if ($city->airports()->exists()) {
            return response()->json([
                'message' => 'Cannot delete city with associated airports'
            ], 422);
        }

        $city->delete();

        return response()->json([
            'message' => 'City deleted successfully'
        ]);
    }

    public function toggleStatus($id)
    {
        $city = City::findOrFail($id);
        $city->is_active = !$city->is_active;
        $city->save();

        return response()->json([
            'message' => 'City status updated successfully',
            'data' => $city
        ]);
    }

    // BulkUploadable implementation
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
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'state_province' => 'nullable|string|max:255',
            'timezone' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_capital' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ];
    }

    protected function saveRecord(array $data)
    {
        return City::create($data);
    }

    protected function getColumnDescriptions(): array
    {
        return [
            'country_code' => 'Country code (2-letter ISO code, e.g., BD, AE, GB)',
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
            [
                'country_code' => 'GB',
                'name' => 'London',
                'name_bn' => 'লন্ডন',
                'state_province' => 'England',
                'timezone' => 'Europe/London',
                'latitude' => '51.5074',
                'longitude' => '-0.1278',
                'is_capital' => '1',
                'is_active' => '1',
            ],
        ];
    }

    protected function validateRow(array $row, int $rowNumber): ?string
    {
        // Check if country exists
        $country = Country::where('code', $row['country_code'])->first();
        if (!$country) {
            return "Row {$rowNumber}: Country with code '{$row['country_code']}' not found";
        }

        // Validate latitude
        if (isset($row['latitude']) && !empty($row['latitude'])) {
            if (!is_numeric($row['latitude']) || $row['latitude'] < -90 || $row['latitude'] > 90) {
                return "Row {$rowNumber}: Invalid latitude value";
            }
        }

        // Validate longitude
        if (isset($row['longitude']) && !empty($row['longitude'])) {
            if (!is_numeric($row['longitude']) || $row['longitude'] < -180 || $row['longitude'] > 180) {
                return "Row {$rowNumber}: Invalid longitude value";
            }
        }

        return null;
    }

    protected function transformRowData(array $row): array
    {
        // Convert country_code to country_id
        $country = Country::where('code', $row['country_code'])->first();

        $data = [
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

        return $data;
    }

    protected function getExportQuery(Request $request)
    {
        return City::with('country')->active();
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

    protected function prepareExportRow($city): array
    {
        return [
            'country_code' => $city->country->code ?? '',
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
