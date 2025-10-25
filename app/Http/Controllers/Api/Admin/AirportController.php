<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Airport;
use Illuminate\Http\Request;

class AirportController extends Controller
{
    public function index(Request $request)
    {
        $airports = Airport::query()
            ->with(['country']) // Eager load country
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->paginate(10)
            ->withQueryString();

        return $airports;
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:airports',
            'country_id' => 'required|exists:countries,id',
            // 'city_id' => 'nullable|exists:cities,id', // We can add this later
        ]);

        Airport::create($request->all());

        return response()->json(['message' => 'Airport created successfully.']);
    }

    public function show(Airport $airport)
    {
        return $airport->load('country');
    }

    public function update(Request $request, Airport $airport)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:airports,code,' . $airport->id,
            'country_id' => 'required|exists:countries,id',
            // 'city_id' => 'nullable|exists:cities,id',
        ]);

        $airport->update($request->all());

        return response()->json(['message' => 'Airport updated successfully.']);
    }

    public function destroy(Airport $airport)
    {
        $airport->delete();

        return response()->json(['message' => 'Airport deleted successfully.']);
    }
}