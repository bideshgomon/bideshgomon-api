<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Airport;
use App\Models\City;
use App\Traits\BulkUploadable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AirportController extends Controller
{
    use BulkUploadable;

    public function index(Request $request)
    {
        $query = Airport::with('city.country');

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('name_bn', 'LIKE', "%{$search}%")
                  ->orWhere('iata_code', 'LIKE', "%{$search}%")
                  ->orWhere('icao_code', 'LIKE', "%{$search}%");
            });
        }

        // Filter by city
        if ($request->has('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Filter by international
        if ($request->has('is_international')) {
            $query->where('is_international', $request->is_international);
        }

        // Sort
        $sortField = $request->get('sort_field', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Paginate
        $perPage = $request->get('per_page', 15);
        $airports = $query->paginate($perPage);

        return response()->json($airports);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city_id' => 'required|exists:cities,id',
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'iata_code' => 'required|string|size:3|unique:airports,iata_code',
            'icao_code' => 'nullable|string|size:4|unique:airports,icao_code',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_international' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $airport = Airport::create($validator->validated());
        $airport->load('city.country');

        return response()->json([
            'message' => 'Airport created successfully',
            'data' => $airport
        ], 201);
    }

    public function show($id)
    {
        $airport = Airport::with('city.country')->findOrFail($id);
        return response()->json($airport);
    }

    public function update(Request $request, $id)
    {
        $airport = Airport::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'city_id' => 'sometimes|required|exists:cities,id',
            'name' => 'sometimes|required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'iata_code' => 'sometimes|required|string|size:3|unique:airports,iata_code,' . $id,
            'icao_code' => 'nullable|string|size:4|unique:airports,icao_code,' . $id,
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_international' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $airport->update($validator->validated());
        $airport->load('city.country');

        return response()->json([
            'message' => 'Airport updated successfully',
            'data' => $airport
        ]);
    }

    public function destroy($id)
    {
        $airport = Airport::findOrFail($id);
        $airport->delete();

        return response()->json([
            'message' => 'Airport deleted successfully'
        ]);
    }

    public function toggleStatus($id)
    {
        $airport = Airport::findOrFail($id);
        $airport->is_active = !$airport->is_active;
        $airport->save();

        return response()->json([
            'message' => 'Airport status updated successfully',
            'data' => $airport
        ]);
    }

    // BulkUploadable implementation
    protected function getModelClass(): string
    {
        return Airport::class;
    }

    protected function getRequiredColumns(): array
    {
        return ['city_name', 'name', 'iata_code'];
    }

    protected function getOptionalColumns(): array
    {
        return ['name_bn', 'icao_code', 'latitude', 'longitude', 'is_international', 'is_active'];
    }

    protected function getTemplateColumns(): array
    {
        return array_merge($this->getRequiredColumns(), $this->getOptionalColumns());
    }

    protected function getValidationRules(): array
    {
        return [
            'city_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'iata_code' => 'required|string|size:3',
            'icao_code' => 'nullable|string|size:4',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_international' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ];
    }

    protected function saveRecord(array $data)
    {
        return Airport::create($data);
    }

    protected function getColumnDescriptions(): array
    {
        return [
            'city_name' => 'City name (must match existing city)',
            'name' => 'Airport name in English',
            'name_bn' => 'Airport name in Bengali (optional)',
            'iata_code' => '3-letter IATA code (e.g., DAC, DXB, LHR)',
            'icao_code' => '4-letter ICAO code (e.g., VGHS, OMDB, EGLL) (optional)',
            'latitude' => 'Latitude coordinate (optional)',
            'longitude' => 'Longitude coordinate (optional)',
            'is_international' => 'Is international airport? (1 or 0, default: 0)',
            'is_active' => 'Is active? (1 or 0, default: 1)',
        ];
    }

    protected function getSampleData(): array
    {
        return [
            [
                'city_name' => 'Dhaka',
                'name' => 'Hazrat Shahjalal International Airport',
                'name_bn' => 'হযরত শাহজালাল আন্তর্জাতিক বিমানবন্দর',
                'iata_code' => 'DAC',
                'icao_code' => 'VGHS',
                'latitude' => '23.8433',
                'longitude' => '90.3978',
                'is_international' => '1',
                'is_active' => '1',
            ],
            [
                'city_name' => 'Dubai',
                'name' => 'Dubai International Airport',
                'name_bn' => 'দুবাই আন্তর্জাতিক বিমানবন্দর',
                'iata_code' => 'DXB',
                'icao_code' => 'OMDB',
                'latitude' => '25.2532',
                'longitude' => '55.3657',
                'is_international' => '1',
                'is_active' => '1',
            ],
            [
                'city_name' => 'London',
                'name' => 'London Heathrow Airport',
                'name_bn' => 'লন্ডন হিথ্রো বিমানবন্দর',
                'iata_code' => 'LHR',
                'icao_code' => 'EGLL',
                'latitude' => '51.4700',
                'longitude' => '-0.4543',
                'is_international' => '1',
                'is_active' => '1',
            ],
        ];
    }

    protected function validateRow(array $row, int $rowNumber): ?string
    {
        // Check if city exists
        $city = City::where('name', $row['city_name'])->first();
        if (!$city) {
            return "Row {$rowNumber}: City '{$row['city_name']}' not found";
        }

        // Validate IATA code
        if (strlen($row['iata_code']) !== 3) {
            return "Row {$rowNumber}: IATA code must be exactly 3 characters";
        }

        // Check IATA uniqueness
        if (Airport::where('iata_code', strtoupper($row['iata_code']))->exists()) {
            return "Row {$rowNumber}: IATA code '{$row['iata_code']}' already exists";
        }

        // Validate ICAO code if provided
        if (isset($row['icao_code']) && !empty($row['icao_code'])) {
            if (strlen($row['icao_code']) !== 4) {
                return "Row {$rowNumber}: ICAO code must be exactly 4 characters";
            }
            
            // Check ICAO uniqueness
            if (Airport::where('icao_code', strtoupper($row['icao_code']))->exists()) {
                return "Row {$rowNumber}: ICAO code '{$row['icao_code']}' already exists";
            }
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
        // Convert city_name to city_id
        $city = City::where('name', $row['city_name'])->first();

        $data = [
            'city_id' => $city->id,
            'name' => $row['name'],
            'name_bn' => $row['name_bn'] ?? null,
            'iata_code' => strtoupper($row['iata_code']),
            'icao_code' => isset($row['icao_code']) && $row['icao_code'] !== '' ? strtoupper($row['icao_code']) : null,
            'latitude' => isset($row['latitude']) && $row['latitude'] !== '' ? $row['latitude'] : null,
            'longitude' => isset($row['longitude']) && $row['longitude'] !== '' ? $row['longitude'] : null,
            'is_international' => isset($row['is_international']) ? (bool)$row['is_international'] : false,
            'is_active' => isset($row['is_active']) ? (bool)$row['is_active'] : true,
        ];

        return $data;
    }

    protected function getExportQuery(Request $request)
    {
        return Airport::with(['city.country'])->active();
    }

    protected function getExportColumns(): array
    {
        return [
            'city_name',
            'name',
            'name_bn',
            'iata_code',
            'icao_code',
            'latitude',
            'longitude',
            'is_international',
            'is_active',
        ];
    }

    protected function prepareExportRow($airport): array
    {
        return [
            'city_name' => $airport->city->name ?? '',
            'name' => $airport->name,
            'name_bn' => $airport->name_bn ?? '',
            'iata_code' => $airport->iata_code,
            'icao_code' => $airport->icao_code ?? '',
            'latitude' => $airport->latitude ?? '',
            'longitude' => $airport->longitude ?? '',
            'is_international' => $airport->is_international ? '1' : '0',
            'is_active' => $airport->is_active ? '1' : '0',
        ];
    }
}
