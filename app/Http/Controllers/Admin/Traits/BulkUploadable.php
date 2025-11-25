<?php

namespace App\Http\Controllers\Admin\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

trait BulkUploadable
{
    /**
     * Show bulk upload form
     */
    public function bulkUpload()
    {
        return Inertia::render($this->bulkUploadView ?? 'Admin/DataManagement/BulkUpload', [
            'entityName' => $this->entityName ?? 'Record',
            'entityNamePlural' => $this->entityNamePlural ?? 'Records',
            'templateColumns' => $this->getTemplateColumns(),
            'sampleData' => $this->getSampleData(),
            'backRoute' => $this->indexRoute ?? 'admin.data.index',
        ]);
    }

    /**
     * Process bulk upload CSV
     */
    public function processBulkUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
        ]);

        try {
            $file = $request->file('file');
            $path = $file->getRealPath();
            $csvData = array_map('str_getcsv', file($path));
            
            // Get headers from first row
            $headers = array_shift($csvData);
            $headers = array_map('trim', $headers);
            
            // Validate headers
            $requiredColumns = $this->getRequiredColumns();
            $missingColumns = array_diff($requiredColumns, $headers);
            
            if (!empty($missingColumns)) {
                return back()->with('error', 'Missing required columns: ' . implode(', ', $missingColumns));
            }

            $successCount = 0;
            $errorCount = 0;
            $errors = [];
            
            DB::beginTransaction();
            
            try {
                foreach ($csvData as $index => $row) {
                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        continue;
                    }
                    
                    // Combine headers with row data
                    $data = array_combine($headers, $row);
                    $lineNumber = $index + 2; // +2 because array starts at 0 and we removed header
                    
                    // Validate row data
                    $validator = Validator::make($data, $this->getValidationRules());
                    
                    if ($validator->fails()) {
                        $errorCount++;
                        $errors[] = "Line {$lineNumber}: " . implode(', ', $validator->errors()->all());
                        
                        // Stop if too many errors
                        if ($errorCount > 100) {
                            $errors[] = "Too many errors. Upload stopped. Please fix the CSV and try again.";
                            break;
                        }
                        continue;
                    }
                    
                    // Transform data if needed
                    $transformedData = $this->transformRowData($data);
                    
                    // Save to database
                    try {
                        $this->saveRecord($transformedData);
                        $successCount++;
                    } catch (\Exception $e) {
                        $errorCount++;
                        $errors[] = "Line {$lineNumber}: " . $e->getMessage();
                    }
                }
                
                if ($errorCount > 0 && $successCount === 0) {
                    DB::rollBack();
                    return back()->with('error', 'Upload failed. Errors: ' . implode("\n", array_slice($errors, 0, 10)));
                }
                
                DB::commit();
                
                $message = "Successfully imported {$successCount} records.";
                if ($errorCount > 0) {
                    $message .= " {$errorCount} records failed.";
                }
                
                return redirect()->route($this->indexRoute ?? 'admin.data.index')
                    ->with('success', $message)
                    ->with('uploadErrors', $errors);
                    
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
            
        } catch (\Exception $e) {
            return back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Download CSV template
     */
    public function downloadTemplate()
    {
        $columns = $this->getTemplateColumns();
        $sampleData = $this->getSampleData();
        
        $filename = Str::slug($this->entityNamePlural ?? 'data') . '_template.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($columns, $sampleData) {
            $file = fopen('php://output', 'w');
            
            // Write headers
            fputcsv($file, $columns);
            
            // Write sample data
            foreach ($sampleData as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export data to CSV
     */
    public function export(Request $request)
    {
        $query = $this->getExportQuery($request);
        $data = $query->get();
        
        $filename = Str::slug($this->entityNamePlural ?? 'data') . '_export_' . now()->format('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $columns = $this->getExportColumns();
        
        $callback = function() use ($data, $columns) {
            $file = fopen('php://output', 'w');
            
            // Write headers
            fputcsv($file, $columns);
            
            // Write data
            foreach ($data as $record) {
                $row = $this->prepareExportRow($record);
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get template columns - must be implemented by controller
     */
    abstract protected function getTemplateColumns(): array;

    /**
     * Get required columns for validation - must be implemented by controller
     */
    abstract protected function getRequiredColumns(): array;

    /**
     * Get validation rules for CSV data - must be implemented by controller
     */
    abstract protected function getValidationRules(): array;

    /**
     * Get sample data for template - must be implemented by controller
     */
    abstract protected function getSampleData(): array;

    /**
     * Transform row data before saving - can be overridden by controller
     */
    protected function transformRowData(array $data): array
    {
        return $data;
    }

    /**
     * Save a single record - must be implemented by controller
     */
    abstract protected function saveRecord(array $data);

    /**
     * Get query for export - must be implemented by controller
     */
    abstract protected function getExportQuery(Request $request);

    /**
     * Get columns for export - must be implemented by controller
     */
    abstract protected function getExportColumns(): array;

    /**
     * Prepare a row for export - must be implemented by controller
     */
    abstract protected function prepareExportRow($record): array;
}
