<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Airline;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AirlineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Airline::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('iata_code', 'like', '%'.$request->search.'%');
        }

        return $query->orderBy('name')->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:airlines',
            'iata_code' => 'required|string|size:2|unique:airlines',
        ]);

        $airline = Airline::create($validated);

        return response()->json($airline, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Airline $airline)
    {
        return $airline;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Airline $airline)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('airlines')->ignore($airline->id)],
            'iata_code' => ['required', 'string', 'size:2', Rule::unique('airlines')->ignore($airline->id)],
        ]);

        $airline->update($validated);

        return response()->json($airline);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Airline $airline)
    {
        $airline->delete();

        return response()->json(null, 204); // No Content
    }
}
