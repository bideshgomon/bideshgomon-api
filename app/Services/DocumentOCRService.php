<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DocumentOCRService
{
    /**
     * Process document using available OCR method.
     */
    public function processDocument(string $imagePath, string $documentType): array
    {
        $startTime = microtime(true);
        
        try {
            // Extract metadata for fraud detection
            $metadata = $this->extractMetadata($imagePath);
            
            // Preprocess image for better OCR
            $processedPath = $this->preprocessImage($imagePath);

            // Try OCR methods in order of preference
            $result = $this->tryGoogleVisionOCR($processedPath, $documentType);
            
            if (!$result['success']) {
                $result = $this->tryTesseractOCR($processedPath, $documentType);
            }

            if (!$result['success']) {
                $result = $this->tryBasicExtraction($processedPath, $documentType);
            }
            
            // Add processing time and metadata to result
            $result['processing_time'] = round(microtime(true) - $startTime, 2);
            $result['metadata'] = $metadata;

            return $result;
        } catch (\Exception $e) {
            Log::error('Document OCR processing failed', [
                'error' => $e->getMessage(),
                'image_path' => $imagePath,
                'document_type' => $documentType,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'method' => 'none',
                'processing_time' => round(microtime(true) - $startTime, 2),
            ];
        }
    }

    /**
     * Extract metadata from document image for fraud detection.
     */
    protected function extractMetadata(string $imagePath): array
    {
        $metadata = [
            'file_size' => filesize($imagePath),
            'mime_type' => mime_content_type($imagePath),
            'file_modified' => date('Y-m-d H:i:s', filemtime($imagePath)),
            'fraud_indicators' => [],
        ];
        
        try {
            // Get image dimensions using native PHP
            $imageInfo = getimagesize($imagePath);
            if ($imageInfo) {
                $metadata['width'] = $imageInfo[0];
                $metadata['height'] = $imageInfo[1];
                $metadata['aspect_ratio'] = round($imageInfo[0] / $imageInfo[1], 2);
            }
            
            // Extract EXIF data if available
            $exifData = @exif_read_data($imagePath);
            if ($exifData) {
                $metadata['exif'] = [
                    'make' => $exifData['Make'] ?? null,
                    'model' => $exifData['Model'] ?? null,
                    'datetime' => $exifData['DateTime'] ?? null,
                    'software' => $exifData['Software'] ?? null,
                    'gps' => isset($exifData['GPSLatitude']) ? 'Available' : 'Not Available',
                ];
                
                // Fraud detection indicators
                if (isset($exifData['Software'])) {
                    $software = strtolower($exifData['Software']);
                    if (strpos($software, 'photoshop') !== false || 
                        strpos($software, 'gimp') !== false || 
                        strpos($software, 'paint') !== false) {
                        $metadata['fraud_indicators'][] = 'Image edited with: ' . $exifData['Software'];
                    }
                }
                
                // Check if datetime is too recent (just created)
                if (isset($exifData['DateTime'])) {
                    $exifTime = strtotime($exifData['DateTime']);
                    if ($exifTime && time() - $exifTime < 300) { // Created within 5 minutes
                        $metadata['fraud_indicators'][] = 'Image created very recently (within 5 minutes)';
                    }
                }
            } else {
                $metadata['fraud_indicators'][] = 'No EXIF data found (possible edited/screenshot)';
            }
            
            // Check for suspiciously small/large file sizes
            if (isset($metadata['width'], $metadata['height'])) {
                $expectedSize = ($metadata['width'] * $metadata['height']) / 10;
                if ($metadata['file_size'] < $expectedSize * 0.1) {
                    $metadata['fraud_indicators'][] = 'Unusually small file size for image dimensions';
                }
            }
            
        } catch (\Exception $e) {
            Log::warning('Metadata extraction failed', ['error' => $e->getMessage()]);
            $metadata['extraction_error'] = $e->getMessage();
        }
        
        return $metadata;
    }
    
    /**
     * Preprocess image for better OCR results.
     */
    protected function preprocessImage(string $imagePath): string
    {
        // Skip preprocessing if Intervention Image not available
        return $imagePath;
        
        try {
            // $image = Image::make($imagePath);

            // Resize if too large
            if ($image->width() > 2000) {
                $image->resize(2000, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
            }

            // Enhance contrast and brightness
            $image->contrast(15);
            $image->brightness(5);
            $image->sharpen(10);

            // Convert to grayscale for better text recognition
            $image->greyscale();

            // Save processed image
            $processedPath = str_replace('.', '_processed.', $imagePath);
            $image->save($processedPath, 90);

            return $processedPath;
        } catch (\Exception $e) {
            Log::warning('Image preprocessing failed, using original', [
                'error' => $e->getMessage(),
            ]);
            return $imagePath;
        }
    }

    /**
     * Try Google Vision API for OCR.
     */
    protected function tryGoogleVisionOCR(string $imagePath, string $documentType): array
    {
        $apiKey = config('services.google_vision.api_key');

        if (!$apiKey) {
            return ['success' => false, 'error' => 'Google Vision API key not configured'];
        }

        try {
            $imageContent = base64_encode(file_get_contents($imagePath));

            $response = Http::timeout(30)->post(
                "https://vision.googleapis.com/v1/images:annotate?key={$apiKey}",
                [
                    'requests' => [
                        [
                            'image' => ['content' => $imageContent],
                            'features' => [
                                ['type' => 'DOCUMENT_TEXT_DETECTION'],
                                ['type' => 'TEXT_DETECTION'],
                            ],
                        ],
                    ],
                ]
            );

            if ($response->successful()) {
                $data = $response->json();
                $fullText = $data['responses'][0]['fullTextAnnotation']['text'] ?? '';

                $extractedData = $this->parseDocumentData($fullText, $documentType);
                $confidence = $this->calculateConfidence($data['responses'][0] ?? []);

                return [
                    'success' => true,
                    'method' => 'google_vision',
                    'extracted_data' => $extractedData,
                    'confidence_score' => $confidence,
                    'raw_text' => $fullText,
                    'estimated_time' => '5-15 seconds',
                ];
            }

            return ['success' => false, 'error' => 'Google Vision API request failed'];
        } catch (\Exception $e) {
            Log::error('Google Vision OCR failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Try Tesseract OCR (fallback method).
     */
    protected function tryTesseractOCR(string $imagePath, string $documentType): array
    {
        if (!function_exists('exec') || !$this->isTesseractInstalled()) {
            return ['success' => false, 'error' => 'Tesseract not available'];
        }

        try {
            $outputFile = sys_get_temp_dir() . '/' . uniqid('ocr_');
            $command = "tesseract " . escapeshellarg($imagePath) . " " . escapeshellarg($outputFile) . " 2>&1";
            
            exec($command, $output, $returnCode);

            if ($returnCode === 0 && file_exists($outputFile . '.txt')) {
                $text = file_get_contents($outputFile . '.txt');
                @unlink($outputFile . '.txt');

                $extractedData = $this->parseDocumentData($text, $documentType);

                return [
                    'success' => true,
                    'method' => 'tesseract',
                    'extracted_data' => $extractedData,
                    'confidence_score' => 65.0, // Tesseract doesn't provide confidence
                    'raw_text' => $text,
                    'estimated_time' => '10-30 seconds',
                ];
            }

            return ['success' => false, 'error' => 'Tesseract processing failed'];
        } catch (\Exception $e) {
            Log::error('Tesseract OCR failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Basic extraction using pattern matching (last resort).
     */
    protected function tryBasicExtraction(string $imagePath, string $documentType): array
    {
        // This would be a fallback that prompts user for manual entry
        return [
            'success' => false,
            'error' => 'Automatic extraction failed, manual entry required',
            'method' => 'manual_required',
        ];
    }

    /**
     * Parse extracted text based on document type.
     */
    protected function parseDocumentData(string $text, string $documentType): array
    {
        $data = [];

        switch ($documentType) {
            case 'passport':
                $data = $this->parsePassport($text);
                break;
            case 'national_id':
                $data = $this->parseNationalID($text);
                break;
            case 'visa':
                $data = $this->parseVisa($text);
                break;
            default:
                $data = ['raw_text' => $text];
        }

        return $data;
    }

    /**
     * Parse passport data from OCR text.
     */
    protected function parsePassport(string $text): array
    {
        $data = [];

        // Passport number (common formats: A1234567, AB123456, etc.)
        if (preg_match('/[A-Z]{1,2}\d{6,8}/', $text, $matches)) {
            $data['passport_number'] = $matches[0];
        }

        // Name patterns (SURNAME, Given Names format)
        if (preg_match('/Surname[:\s]*([A-Z\s]+)/i', $text, $matches)) {
            $data['surname'] = trim($matches[1]);
        }

        if (preg_match('/Given Names[:\s]*([A-Z\s]+)/i', $text, $matches)) {
            $data['given_names'] = trim($matches[1]);
        }

        // Date of birth (multiple formats)
        if (preg_match('/\b\d{1,2}[\s\/-]\d{1,2}[\s\/-]\d{4}\b/', $text, $matches)) {
            $data['date_of_birth'] = $matches[0];
        }

        // Nationality
        if (preg_match('/Nationality[:\s]*([A-Z\s]+)/i', $text, $matches)) {
            $data['nationality'] = trim($matches[1]);
        }

        // Sex/Gender
        if (preg_match('/Sex[:\s]*([MF])/i', $text, $matches)) {
            $data['sex'] = strtoupper($matches[1]);
        }

        // Issue date
        if (preg_match('/Date of Issue[:\s]*(\d{1,2}[\s\/-]\d{1,2}[\s\/-]\d{4})/i', $text, $matches)) {
            $data['issue_date'] = $matches[1];
        }

        // Expiry date
        if (preg_match('/Date of Expiry[:\s]*(\d{1,2}[\s\/-]\d{1,2}[\s\/-]\d{4})/i', $text, $matches)) {
            $data['expiry_date'] = $matches[1];
        }

        // Place of birth
        if (preg_match('/Place of Birth[:\s]*([A-Z\s,]+)/i', $text, $matches)) {
            $data['place_of_birth'] = trim($matches[1]);
        }

        return $data;
    }

    /**
     * Parse national ID data from OCR text.
     */
    protected function parseNationalID(string $text): array
    {
        $data = [];

        // National ID number (adjust pattern based on country)
        if (preg_match('/\b\d{10,17}\b/', $text, $matches)) {
            $data['id_number'] = $matches[0];
        }

        // Name
        if (preg_match('/Name[:\s]*([A-Z\s]+)/i', $text, $matches)) {
            $data['name'] = trim($matches[1]);
        }

        // Date of birth
        if (preg_match('/\b\d{1,2}[\s\/-]\d{1,2}[\s\/-]\d{4}\b/', $text, $matches)) {
            $data['date_of_birth'] = $matches[0];
        }

        // Address (basic pattern)
        if (preg_match('/Address[:\s]*([A-Za-z0-9\s,.-]+)/i', $text, $matches)) {
            $data['address'] = trim($matches[1]);
        }

        return $data;
    }

    /**
     * Parse visa data from OCR text.
     */
    protected function parseVisa(string $text): array
    {
        $data = [];

        // Visa number
        if (preg_match('/Visa\s*(?:Number|No)[:\s]*([A-Z0-9]+)/i', $text, $matches)) {
            $data['visa_number'] = trim($matches[1]);
        }

        // Visa type
        if (preg_match('/Type[:\s]*([A-Z0-9\s]+)/i', $text, $matches)) {
            $data['visa_type'] = trim($matches[1]);
        }

        // Valid from
        if (preg_match('/Valid From[:\s]*(\d{1,2}[\s\/-]\d{1,2}[\s\/-]\d{4})/i', $text, $matches)) {
            $data['valid_from'] = $matches[1];
        }

        // Valid until
        if (preg_match('/Valid Until[:\s]*(\d{1,2}[\s\/-]\d{1,2}[\s\/-]\d{4})/i', $text, $matches)) {
            $data['valid_until'] = $matches[1];
        }

        return $data;
    }

    /**
     * Calculate confidence score from Google Vision response.
     */
    protected function calculateConfidence(array $response): float
    {
        if (empty($response['fullTextAnnotation'])) {
            return 0.0;
        }

        $pages = $response['fullTextAnnotation']['pages'] ?? [];
        $totalConfidence = 0;
        $count = 0;

        foreach ($pages as $page) {
            foreach ($page['blocks'] ?? [] as $block) {
                if (isset($block['confidence'])) {
                    $totalConfidence += $block['confidence'];
                    $count++;
                }
            }
        }

        return $count > 0 ? round(($totalConfidence / $count) * 100, 2) : 0.0;
    }

    /**
     * Check if Tesseract is installed.
     */
    protected function isTesseractInstalled(): bool
    {
        exec('tesseract --version 2>&1', $output, $returnCode);
        return $returnCode === 0;
    }
}
