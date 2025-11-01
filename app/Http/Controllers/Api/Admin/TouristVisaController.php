<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\TouristVisa;
use App\Models\TouristVisaDocument; // Import document model
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TouristVisaController extends Controller
{
    /**
     * Display a listing of the tourist visa applications.
     */
    public function index(Request $request): JsonResponse
    {
        $query = TouristVisa::with(['user:id,name', 'destinationCountry:id,name'])
            ->latest(); // Order by most recent

        // Basic Search (Example: by user name or country name)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn ($subQ) => $subQ->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('destinationCountry', fn ($subQ) => $subQ->where('name', 'like', "%{$search}%"));
            });
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $visas = $query->paginate(15)->withQueryString();

        return response()->json($visas);
    }

    /**
     * Display the specified tourist visa application (already handled by PageController's show method)
     * We might not need a separate API endpoint just for showing details initially.
     */
    // public function show(TouristVisa $touristVisa) { ... }

    /**
     * Update the status or notes of the specified tourist visa application.
     */
    public function update(Request $request, TouristVisa $touristVisa): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'sometimes|required|string|in:pending,submitted,processing,approved,rejected,cancelled', // Example statuses
            'admin_notes' => 'nullable|string',
        ]);

        $touristVisa->update($validated);

        return response()->json($touristVisa->fresh([ // Return fresh data
            'user:id,name,email',
            'destinationCountry:id,name',
            'documents.documentType:id,name',
            'documents.userDocument:id,file_path,file_name',
        ]));
    }

    /**
     * Update the status or notes of a specific document within a visa application.
     */
    public function updateDocumentStatus(Request $request, TouristVisaDocument $document): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,submitted,verified,rejected', // Example statuses
            'admin_notes' => 'nullable|string',
        ]);

        $document->update($validated);

        // Return the updated document with its type
        return response()->json($document->load('documentType:id,name'));
    }

    /**
     * Remove the specified tourist visa application from storage.
     */
    public function destroy(TouristVisa $touristVisa): JsonResponse
    {
        // Maybe add authorization checks here later
        $touristVisa->delete();

        return response()->json(null, 204); // No content response
    }
}
