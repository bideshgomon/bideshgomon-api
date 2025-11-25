<?php

namespace App\Http\Controllers;

use App\Models\UserDocument;
use App\Services\DocumentVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = UserDocument::where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        return Inertia::render('Documents/Index', [
            'documents' => $documents,
            'supportedTypes' => UserDocument::supportedTypes(),
        ]);
    }

    public function store(Request $request, DocumentVerificationService $service)
    {
        $request->validate([
            'document_type' => 'required|string|max:100',
            'file' => 'required|file|max:5120', // 5MB limit for now
            'expires_at' => 'nullable|date',
            'is_primary' => 'nullable|boolean',
        ]);

        if (!in_array($request->document_type, UserDocument::supportedTypes())) {
            return back()->withErrors(['document_type' => 'Unsupported document type']);
        }

        $service->upload(Auth::id(), $request->file('file'), $request->document_type, [
            'expires_at' => $request->expires_at,
            'is_primary' => (bool)$request->is_primary,
            'meta' => [],
        ]);

        return redirect()->route('documents.index')->with('success', 'Document uploaded successfully and pending verification.');
    }

    public function destroy(UserDocument $document)
    {
        if ($document->user_id !== Auth::id()) {
            abort(403);
        }
        app(DocumentVerificationService::class)->delete($document);
        return redirect()->route('documents.index')->with('success', 'Document deleted');
    }

    public function download(UserDocument $document)
    {
        if ($document->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to document');
        }

        // Try both storage_path (old) and file_path (new) fields
        $filePath = $document->file_path ?? $document->storage_path;
        
        if (!$filePath || !Storage::disk('private')->exists($filePath)) {
            abort(404, 'File not found');
        }

        $fileName = $document->file_name ?? $document->original_filename ?? 'document.' . $document->file_type;
        
        return response()->download(Storage::disk('private')->path($filePath), $fileName);
    }
}
