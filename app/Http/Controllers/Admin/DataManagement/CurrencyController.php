<?php

namespace App\Http\Controllers\Admin\DataManagement;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Traits\BulkUploadable;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CurrencyController extends Controller
{
    use BulkUploadable;

    protected $entityName = 'Currency';
    protected $entityNamePlural = 'Currencies';
    protected $indexRoute = 'admin.data.currencies.index';
    protected $bulkUploadView = 'Admin/DataManagement/Currencies/BulkUpload';

    /**
     * Display a listing of currencies
     */
    public function index(Request $request)
    {
        $query = Currency::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('symbol', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'true');
        }

        // Sort
        $sortField = $request->get('sort', 'code');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $currencies = $query->paginate($request->get('per_page', 15))
            ->withQueryString();

        return Inertia::render('Admin/DataManagement/Currencies/Index', [
            'currencies' => $currencies,
            'filters' => $request->only(['search', 'is_active']),
            'sort' => [
                'field' => $sortField,
                'direction' => $sortDirection,
            ],
        ]);
    }

    /**
     * Show the form for creating a new currency
     */
    public function create()
    {
        return Inertia::render('Admin/DataManagement/Currencies/Create');
    }

    /**
     * Store a newly created currency
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:3|unique:currencies,code',
            'name' => 'required|string|max:100',
            'symbol' => 'required|string|max:10',
            'exchange_rate_to_bdt' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        try {
            // Uppercase currency code
            $validated['code'] = strtoupper($validated['code']);

            Currency::create($validated);

            return redirect()->route('admin.data.currencies.index')
                ->with('success', 'Currency created successfully.');
        } catch (\Exception $e) {
            Log::error('Currency creation failed', [
                'error' => $e->getMessage(),
                'data' => $validated,
            ]);

            return back()->withInput()->with('error', 'Failed to create currency: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified currency
     */
    public function show(Currency $currency)
    {
        return Inertia::render('Admin/DataManagement/Currencies/Show', [
            'currency' => $currency,
        ]);
    }

    /**
     * Show the form for editing the specified currency
     */
    public function edit(Currency $currency)
    {
        return Inertia::render('Admin/DataManagement/Currencies/Edit', [
            'currency' => $currency,
        ]);
    }

    /**
     * Update the specified currency
     */
    public function update(Request $request, Currency $currency)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'size:3', Rule::unique('currencies')->ignore($currency->id)],
            'name' => 'required|string|max:100',
            'symbol' => 'required|string|max:10',
            'exchange_rate_to_bdt' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        try {
            // Uppercase currency code
            $validated['code'] = strtoupper($validated['code']);

            $currency->update($validated);

            return redirect()->route('admin.data.currencies.index')
                ->with('success', 'Currency updated successfully.');
        } catch (\Exception $e) {
            Log::error('Currency update failed', [
                'currency_id' => $currency->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withInput()->with('error', 'Failed to update currency: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified currency
     */
    public function destroy(Currency $currency)
    {
        try {
            $currency->delete();

            return redirect()->route('admin.data.currencies.index')
                ->with('success', 'Currency deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Currency deletion failed', [
                'currency_id' => $currency->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to delete currency: ' . $e->getMessage());
        }
    }

    /**
     * Toggle currency status
     */
    public function toggleStatus(Currency $currency)
    {
        try {
            $currency->update(['is_active' => !$currency->is_active]);

            return back()->with('success', 'Currency status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Currency status toggle failed', [
                'currency_id' => $currency->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to update currency status.');
        }
    }

    // BulkUploadable trait implementations

    protected function getTemplateColumns(): array
    {
        return [
            'code',
            'name',
            'symbol',
            'exchange_rate_to_bdt',
            'is_active'
        ];
    }

    protected function getRequiredColumns(): array
    {
        return [
            'code',
            'name',
            'symbol',
            'exchange_rate_to_bdt'
        ];
    }

    protected function getValidationRules(): array
    {
        return [
            'code' => 'required|string|size:3',
            'name' => 'required|string|max:100',
            'symbol' => 'required|string|max:10',
            'exchange_rate_to_bdt' => 'required|numeric|min:0',
            'is_active' => 'nullable|in:true,false,1,0,yes,no',
        ];
    }

    protected function getSampleData(): array
    {
        return [
            ['BDT', 'Bangladeshi Taka', '৳', '1.00', 'true'],
            ['USD', 'US Dollar', '$', '110.50', 'true'],
            ['EUR', 'Euro', '€', '120.75', 'true'],
            ['GBP', 'British Pound', '£', '140.25', 'true'],
        ];
    }

    protected function transformRowData(array $data): array
    {
        // Convert is_active to boolean
        if (isset($data['is_active'])) {
            $data['is_active'] = in_array(strtolower($data['is_active']), ['true', '1', 'yes']);
        }

        // Uppercase currency code
        if (isset($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }

        // Convert exchange rate to float
        if (isset($data['exchange_rate_to_bdt'])) {
            $data['exchange_rate_to_bdt'] = (float) $data['exchange_rate_to_bdt'];
        }

        return $data;
    }

    protected function saveRecord(array $data)
    {
        Currency::updateOrCreate(
            ['code' => $data['code']],
            $data
        );
    }

    protected function getExportQuery(Request $request)
    {
        $query = Currency::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('symbol', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'true');
        }

        return $query->orderBy('code');
    }

    protected function getExportColumns(): array
    {
        return $this->getTemplateColumns();
    }

    protected function prepareExportRow($record): array
    {
        return [
            $record->code,
            $record->name,
            $record->symbol,
            $record->exchange_rate_to_bdt,
            $record->is_active ? 'true' : 'false',
        ];
    }
}
