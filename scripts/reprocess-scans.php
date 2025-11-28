<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DocumentScan;
use App\Services\DocumentOCRService;

$ocrService = app(DocumentOCRService::class);

$scans = DocumentScan::whereIn('status', ['processing', 'pending'])
    ->get();

echo "Found {$scans->count()} stuck documents\n";

foreach ($scans as $scan) {
    echo "Processing scan #{$scan->id} ({$scan->document_type})...\n";
    
    try {
        $imagePath = storage_path('app/public/' . $scan->original_image);
        
        if (!file_exists($imagePath)) {
            echo "  ❌ Image file not found\n";
            $scan->update([
                'status' => 'failed',
                'error_message' => 'Image file not found'
            ]);
            continue;
        }
        
        $result = $ocrService->processDocument($imagePath, $scan->document_type);
        
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
            echo "  ✅ Success! Time: {$result['processing_time']}s\n";
        } else {
            $scan->update([
                'status' => 'failed',
                'error_message' => $result['error'] ?? 'Unknown error',
                'processing_time' => $result['processing_time'] ?? null,
                'processed_at' => now(),
            ]);
            echo "  ❌ Failed: {$result['error']}\n";
        }
        
    } catch (\Exception $e) {
        echo "  ❌ Error: " . $e->getMessage() . "\n";
        $scan->update([
            'status' => 'failed',
            'error_message' => $e->getMessage()
        ]);
    }
}

echo "\n✅ Done! Refresh the page to see results.\n";
