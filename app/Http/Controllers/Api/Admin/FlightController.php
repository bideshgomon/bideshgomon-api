<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Flight;
use Illuminate\Http\Request;

class FlightController extends Controller
{
    public function index(Request $request)
    {
        $flights = Flight::query()
            ->with(['airline', 'originAirport', 'destinationAirport']) // Eager load
            ->when($request->input('search'), function ($query, $search) {
                $query->where('flight_number', 'like', "%{$search}%")
                    ->orWhereHas('airline', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->paginate(10)
            ->withQueryString();

        return $flights;
    }

    public function store(Request $request)
    {
        $request->validate([
            'airline_id' => 'required|exists:airlines,id',
            'origin_airport_id' => 'required|exists:airports,id',
            'destination_airport_id' => 'required|exists:airports,id|different:origin_airport_id',
            'flight_number' => 'required|string|max:255|unique:flights',
            'departure_at' => 'required|date|after:now',
            'arrival_at' => 'required|date|after:departure_at',
            'price' => 'required|numeric|min:0',
            'available_seats' => 'required|integer|min:0',
        ]);

        Flight::create($request->all());

        return response()->json(['message' => 'Flight created successfully.']);
    }

    public function show(Flight $flight)
    {
        return $flight->load(['airline', 'originAirport', 'destinationAirport']);
    }

    public function update(Request $request, Flight $flight)
    {
        $request->validate([
            'airline_id' => 'required|exists:airlines,id',
            'origin_airport_id' => 'required|exists:airports,id',
            'destination_airport_id' => 'required|exists:airports,id|different:origin_airport_id',
            'flight_number' => 'required|string|max:255|unique:flights,flight_number,'.$flight->id,
            'departure_at' => 'required|date',
            'arrival_at' => 'required|date|after:departure_at',
            'price' => 'required|numeric|min:0',
            'available_seats' => 'required|integer|min:0',
        ]);

        $flight->update($request->all());

        return response()->json(['message' => 'Flight updated successfully.']);
    }

    public function destroy(Flight $flight)
    {
        $flight->delete();

        return response()->json(['message' => 'Flight deleted successfully.']);
    }
}
