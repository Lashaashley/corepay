<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Agents; // Your Employee model
use App\Models\Registration;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

use Illuminate\Support\Facades\Auth;

class ImportController extends Controller
{
    /**
     * Import employees from Excel file with streaming progress
     */
    public function importEmployees(Request $request)
    {
        // Disable output buffering for streaming
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        // Set headers for streaming JSON
        header('Content-Type: application/json');
        header('X-Accel-Buffering: no');
        header('Cache-Control: no-cache');
        
        // Disable buffering
        ini_set('output_buffering', 'off');
        ini_set('zlib.output_compression', 'off');
        if (function_exists('apache_setenv')) {
            apache_setenv('no-gzip', '1');
        }

        try {
            // Validate file upload
            $request->validate([
                'excelFile' => 'required|file|mimes:xlsx,xls|max:10240' // Max 10MB
            ]);

            $file = $request->file('excelFile');
            $filePath = $file->getRealPath();
            $ext = strtolower($file->getClientOriginalExtension());

            // Choose appropriate reader
            $reader = ($ext === 'xls') ? new Xls() : new Xlsx();
            
            // Load spreadsheet
            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Check if file has data
            if (count($rows) <= 1) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Excel file appears empty or missing data rows."
                ]);
                return;
            }

            // Remove header row
            $header = array_shift($rows);
            $total = count($rows);
            $current = 0;
            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            // Start transaction
            DB::beginTransaction();

            foreach ($rows as $rowIndex => $row) {
                $current++;
                
                try {
                    // Extract data
                    // Extract data
$empId   = $this->getCellValue($row, 0);
$name    = $this->getCellValue($row, 1);
$swift   = $this->getCellValue($row, 2);
$bank    = $this->getCellValue($row, 3);
$bankcode    = $this->getCellValue($row, 4);
$accno   = $this->getCellValue($row, 5);
$kra     = $this->getCellValue($row, 6);
$email     = $this->getCellValue($row, 7);

// Skip empty rows
if (!$empId) {
    continue;
}

// Split name into FirstName and LastName
$firstName = $name;
$lastName = null;

if ($name && strpos($name, ' ') !== false) {
    $nameParts = explode(' ', trim($name), 2);
    $firstName = trim($nameParts[0]);
    $lastName = trim($nameParts[1]);
}

// Update or create employee
Agents::updateOrCreate(
    ['emp_id' => $empId],
    [
        'FirstName' => $firstName,
        'LastName' => $lastName,
        'Status' => 'ACTIVE',
        'Department' => '1',
        'brid' => '1',
        'EmialID' => $email
    ]
);

                    // Update or create registration
                    Registration::updateOrCreate(
                        ['empid' => $empId],
                        [
                            'kra' => $kra,
                            'Bank' => $bank,
                            'BankCode' => $bankcode,
                            'swiftcode' => $swift,
                            'AccountNo' => $accno,
                            'paymode' => 'Etransfer',
                            'contractor' => 'YES',
                            'unionized' => 'NO',
                            'payrolty' => '14',
                            'penyes' => 'NO',
                            'nssfp' => 'NO',
                            'nhif_shif' => 'NO'
                        ]
                    );

                    $successCount++;

                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Row " . ($rowIndex + 2) . ": " . $e->getMessage();
                    Log::error("Import row error", [
                        'row' => $rowIndex + 2,
                        'error' => $e->getMessage()
                    ]);
                }

                // Send progress updates every 50 rows or at the end
                if ($current % 50 === 0 || $current === $total) {
                    $progressData = [
                        "status" => "progress",
                        "progress" => round(($current / $total) * 100),
                        "message" => "Processed $current of $total rows",
                        "success" => $successCount,
                        "errors" => $errorCount
                    ];
                    
                    echo json_encode($progressData) . "\n";
                    
                    // Force output
                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();
                }
            }

            // Commit transaction
            DB::commit();

            // Clear cache after successful import
            $this->clearImportCache();

            // Log successful import
            Log::info('Employee import completed', [
                //$userId = session('user_id') ?? Auth::id();

                //'user_id' => $userId,
                'total_rows' => $total,
                'success' => $successCount,
                'errors' => $errorCount
            ]);

            // Send final success message
            $finalMessage = [
                "status" => "success",
                "message" => "Data imported successfully!",
                "total" => $total,
                "success" => $successCount,
                "errors" => $errorCount
            ];

            if (!empty($errors) && count($errors) <= 10) {
                $finalMessage['error_details'] = $errors;
            }

            echo json_encode($finalMessage);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            echo json_encode([
                "status" => "error",
                "message" => "Invalid file: " . $e->getMessage()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Import Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            echo json_encode([
                "status" => "error",
                "message" => "Import failed: " . $e->getMessage()
            ]);
        }
    }

    /**
     * Get cell value helper
     */
    private function getCellValue($row, $index)
    {
        return isset($row[$index]) && trim($row[$index]) !== '' ? trim($row[$index]) : null;
    }

    /**
     * Clear import-related cache
     */
    private function clearImportCache()
    {
        try {
            // Clear Laravel cache
            Cache::tags(['periods', 'pname', 'statutory', 'staff'])->flush();
            
            // Clear file-based cache if exists
            $cacheDir = sys_get_temp_dir();
            $cacheFiles = glob($cacheDir . '/{periods,pname,statutory,staff}_*.json', GLOB_BRACE);
            foreach ($cacheFiles as $file) {
                @unlink($file);
            }
        } catch (\Exception $e) {
            Log::warning('Cache clear warning', ['error' => $e->getMessage()]);
        }
    }

   

    /**
     * Download sample Excel template
     */
    public function downloadTemplate()
{
    $headers = [
        'Agent ID',
        'Full Name', 
        'Swift Code',
        'Bank Name',
        'Bank Code',
        'Account Number',
        'KRA PIN',
        'Email'
    ];

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Alternative approach: Set headers using fromArray() method
    $sheet->fromArray($headers, null, 'A1');

    // Add sample data
    $sampleData = [
        ['EMP001', 'John Doe', 'BANKXXX', 'Example Bank', '68', '1234567890', 'A123456789X', 'example@mail.com'],
        ['EMP002', 'Jane Smith', 'BANKYYY', 'Sample Bank', '01', '0987654321', 'B987654321Y', 'example@mail.com']
    ];
    
    // Add sample data starting from row 2
    $sheet->fromArray($sampleData, null, 'A2');

    // Auto-size columns for all columns (A to H since you have 8 columns)
    foreach (range('A', 'H') as $column) {
        $sheet->getColumnDimension($column)->setAutoSize(true);
    }

    // Create writer
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    
    // Generate filename and save to temp file
    $fileName = 'agent_import_template_' . date('Y-m-d') . '.xlsx';
    $tempFile = tempnam(sys_get_temp_dir(), 'excel') . '.xlsx';
    
    $writer->save($tempFile);

    return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
}
}