<?php

namespace App\Http\Controllers\Api\UserProfile;

use App\Http\Controllers\Controller;
use App\Models\UserDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // <-- Import Storage facade

class UserDocumentController extends Controller
{
    /**
     * Display a listing of the user's documents.
     */
    public function index(Request $request)
    {
        // 'with('documentType')' eagerly loads the relationship
        // so we get the name of the document type (e.g., "Passport")
        return $request->user()->documents()
            ->with('documentType')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Store a new document for the user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_type_id' => 'required|exists:document_types,id',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB Max
            'document_number' => 'nullable|string|max:255',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:issue_date',
        ]);

        $user = $request->user();
        $file = $request->file('file');

        // Store the file in 'storage/app/public/documents/user_{id}'
        // The 'public' disk is automatically linked to 'public/storage'
        $path = $file->store('documents/user_' . $user->id, 'public');

        // Create the database record
        $document = $user->documents()->create([
            'document_type_id' => $validated['document_type_id'],
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'document_number' => $validated['document_number'] ?? null,
            'issue_date' => $validated['issue_date'] ?? null,
            'expiry_date' => $validated['expiry_date'] ?? null,
            'status' => 'pending', // Set default status from your migration
        ]);

        // Load the documentType relationship before returning
        return $document->load('documentType');
    }

    /**
     * Remove the specified document from storage.
     */
    public function destroy(Request $request, UserDocument $document)
    {
        // Check if the authenticated user owns this document
        // Using Laravel's authorization (Policies are recommended for more complex cases)
        if ($request->user()->cannot('delete', $document)) {
             // Or check directly: if ($request->user()->id !== $document->user_id)
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // --- UPDATED SECTION ---
        // 1. Delete the file from storage if it exists
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }
        // --- END UPDATED SECTION ---

        // 2. Delete the record from the database
        $document->delete();

        return response()->json(['message' => 'Document deleted successfully']);
    }
}