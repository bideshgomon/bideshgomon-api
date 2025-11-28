<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DocumentScan;
use App\Services\DocumentOCRService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DocumentScanController extends Controller
{
    use AuthorizesRequests;
    
    protected $ocrService;

    public function __construct(DocumentOCRService $ocrService)
    {
        $this->ocrService = $ocrService;
    }

    /**
     * Display document scanner page.
     */
    public function index()
    {
        $scans = auth()->user()
            ->documentScans()
            ->latest()
            ->paginate(10);

        return Inertia::render('User/DocumentScanner/Index', [
            'scans' => $scans,
        ]);
    }

    /**
     * Upload and process document.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'document_image' => 'required|image|mimes:jpeg,png,jpg|max:10240',
            'document_type' => 'required|in:passport,national_id,visa,driving_license,other',
        ]);

        try {
            // Store original image
            $path = $request->file('document_image')->store('document-scans', 'public');
            $fullPath = Storage::disk('public')->path($path);

            // Create scan record
            $scan = auth()->user()->documentScans()->create([
                'document_type' => $request->document_type,
                'original_image' => $path,
                'status' => 'processing',
            ]);

            // Process document asynchronously
            dispatch(function () use ($scan, $fullPath) {
                $this->processDocumentScan($scan, $fullPath);
            })->afterResponse();

            return back()->with('success', 'Document uploaded successfully. Processing...');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to upload document: ' . $e->getMessage());
        }
    }

    /**
     * Process document scan.
     */
    protected function processDocumentScan(DocumentScan $scan, string $imagePath)
    {
        try {
            $result = $this->ocrService->processDocument($imagePath, $scan->document_type);

            if ($result['success']) {
                $scan->update([
                    'extracted_data' => $result['extracted_data'],
                    'metadata' => $result['metadata'] ?? null,
                    'confidence_score' => $result['confidence_score'] ?? null,
                    'processing_time' => $result['processing_time'] ?? null,
                    'processing_method' => $result['method'],
                    'status' => 'completed',
                    'processed_at' => now(),
                ]);

                // If processed image was created, save its path
                if (isset($result['processed_image'])) {
                    $scan->update(['processed_image' => $result['processed_image']]);
                }
            } else {
                $scan->update([
                    'status' => 'failed',
                    'error_message' => $result['error'] ?? 'Unknown error',
                    'processing_time' => $result['processing_time'] ?? null,
                    'processed_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            $scan->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'processed_at' => now(),
            ]);
        }
    }

    /**
     * Show scan details.
     */
    public function show(DocumentScan $scan)
    {
        $this->authorize('view', $scan);

        return Inertia::render('User/DocumentScanner/Show', [
            'scan' => $scan->load('user'),
        ]);
    }

    /**
     * Apply extracted data to user profile.
     */
    public function applyToProfile(Request $request, DocumentScan $scan)
    {
        $this->authorize('update', $scan);

        $request->validate([
            'fields' => 'required|array',
        ]);

        try {
            $user = auth()->user();
            $profile = $user->userProfile;

            if (!$profile) {
                $profile = $user->userProfile()->create([]);
            }

            $extractedData = $scan->extracted_data ?? [];
            $fieldsToApply = $request->fields;

            // Map extracted data to profile fields
            $updates = [];

            foreach ($fieldsToApply as $field) {
                switch ($field) {
                    case 'passport_number':
                        if (isset($extractedData['passport_number'])) {
                            $updates['passport_number'] = $extractedData['passport_number'];
                        }
                        break;
                    case 'date_of_birth':
                        if (isset($extractedData['date_of_birth'])) {
                            $updates['date_of_birth'] = $this->parseDate($extractedData['date_of_birth']);
                        }
                        break;
                    case 'nationality':
                        if (isset($extractedData['nationality'])) {
                            // Try to find country by name
                            $country = \App\Models\Country::where('name', 'like', '%' . $extractedData['nationality'] . '%')->first();
                            if ($country) {
                                $updates['nationality_country_id'] = $country->id;
                            }
                        }
                        break;
                    case 'gender':
                        if (isset($extractedData['sex'])) {
                            $updates['gender'] = strtolower($extractedData['sex']) === 'm' ? 'male' : 'female';
                        }
                        break;
                }
            }

            if (!empty($updates)) {
                $profile->update($updates);

                return back()->with('success', 'Profile updated successfully with extracted data.');
            }

            return back()->with('info', 'No data to apply.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to apply data: ' . $e->getMessage());
        }
    }

    /**
     * Delete a scan.
     */
    public function destroy(DocumentScan $scan)
    {
        $this->authorize('delete', $scan);

        try {
            // Delete images
            if ($scan->original_image) {
                Storage::disk('public')->delete($scan->original_image);
            }
            if ($scan->processed_image) {
                Storage::disk('public')->delete($scan->processed_image);
            }

            $scan->delete();

            return back()->with('success', 'Document scan deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete scan: ' . $e->getMessage());
        }
    }

    /**
     * Re-process a failed scan.
     */
    public function reprocess(DocumentScan $scan)
    {
        $this->authorize('update', $scan);

        if ($scan->status !== 'failed') {
            return back()->with('error', 'Only failed scans can be reprocessed.');
        }

        try {
            $scan->update([
                'status' => 'processing',
                'error_message' => null,
            ]);

            $fullPath = Storage::disk('public')->path($scan->original_image);

            dispatch(function () use ($scan, $fullPath) {
                $this->processDocumentScan($scan, $fullPath);
            })->afterResponse();

            return back()->with('success', 'Reprocessing document...');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to reprocess: ' . $e->getMessage());
        }
    }

    /**
     * Parse date string to Y-m-d format.
     */
    protected function parseDate(string $dateStr): ?string
    {
        try {
            // Try multiple date formats
            $formats = ['d/m/Y', 'd-m-Y', 'm/d/Y', 'm-d-Y', 'Y-m-d'];
            
            foreach ($formats as $format) {
                $date = \DateTime::createFromFormat($format, $dateStr);
                if ($date) {
                    return $date->format('Y-m-d');
                }
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
