<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Airline;
use Illuminate\Http\Request;

class AirlineController extends Controller
{
    public function index(Request $request)
    {
        $airlines = Airline::query()
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->paginate(10)
            ->withQueryString();

        return $airlines;
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:airlines',
        ]);

        Airline::create($request->all());

        return response()->json(['message' => 'Airline created successfully.']);
    }

    public function show(Airline $airline)
    {
        return $airline;
    }

    public function update(Request $request, Airline $airline)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:airlines,code,' . $airline->id,
        ]);

        $airline->update($request->all());

        return response()->json(['message' => 'Airline updated successfully.']);
    }

    public function destroy(Airline $airline)
    {
        $airline->delete();

        return response()->json(['message' => 'Airline deleted successfully.']);
    }
}