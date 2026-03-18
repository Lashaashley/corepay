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
         set_time_limit(300); // or 0 for unlimited (use with caution)
         ini_set('max_execution_time', 300);
        $userId = Auth::id();
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

            // ── Duplicate check ───────────────────────────────────────
            $duplicateRows = $this->detectDuplicates($rows);
            $duplicateIndexes = array_keys($duplicateRows); // ALL duplicate rows now
            if (!empty($duplicateRows)) {
                $reportPath = $this->generateDuplicateReport($duplicateRows);
                session(['duplicate_report_path' => $reportPath]);
                }

            // Start transaction
            DB::beginTransaction();

            foreach ($rows as $rowIndex => $row) {
             $current++;
    
    // Skip ALL duplicate rows (including first occurrences)
    if (in_array($rowIndex, $duplicateIndexes)) {
        $errorCount++;
        $dupInfo = $duplicateRows[$rowIndex];
        $errors[] = "Row " . ($rowIndex + 2) . ": Skipped — " 
                  . ($dupInfo['duplicate_source'] === 'database' ? 'exists in database' : 'duplicate within file')
                  . " (" . $dupInfo['duplicate_field'] . ": " . $dupInfo['duplicate_value'] . ")";
        continue;
    }
                
                try {
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
        'EmailId' => $email
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
    "status"           => "success",
    "message"          => "Import complete. " . (!empty($duplicateRows) ? count($duplicateRows) . " duplicate rows skipped." : ""),
    "total"            => $total,
    "success"          => $successCount,
    "errors"           => $errorCount,
    "has_duplicate_report" => !empty($duplicateRows),
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
    public function downloadDuplicateReport()
{
    $path = session('duplicate_report_path');

    if (!$path || !file_exists($path)) {
        abort(404, 'Report not found or expired.');
    }

    return response()->download($path, 'duplicate_exceptions.xlsx')->deleteFileAfterSend(true);
}
  private function detectDuplicates(array $rows): array
{
    // Track ALL values seen in the file (for cross-checking within file)
    $allFileValues = [
        'empId' => [],
        'accno' => [],
        'kra'   => [],
        'email' => [],
    ];
    
    $duplicateRows = [];

    // Get existing database records with more details
    $existingDbRecords = $this->getExistingDatabaseRecordsWithDetails($rows);
    
    // First pass: Collect ALL values from the file with their row numbers
    foreach ($rows as $rowIndex => $row) {
        $excelRow = $rowIndex + 2;
        
        $checks = [
            'empId' => $this->getCellValue($row, 0),
            'accno' => $this->getCellValue($row, 5),
            'kra'   => $this->getCellValue($row, 6),
            'email' => $this->getCellValue($row, 7),
        ];
        
        // Record all non-empty values with their row numbers
        foreach ($checks as $field => $value) {
            if (!$value) continue;
            
            if (!isset($allFileValues[$field][$value])) {
                $allFileValues[$field][$value] = [];
            }
            $allFileValues[$field][$value][] = $excelRow;
        }
    }
    
    // Second pass: Identify ALL duplicate rows (including first occurrences)
    foreach ($rows as $rowIndex => $row) {
        $excelRow = $rowIndex + 2;

        $checks = [
            'empId' => $this->getCellValue($row, 0),
            'accno' => $this->getCellValue($row, 5),
            'kra'   => $this->getCellValue($row, 6),
            'email' => $this->getCellValue($row, 7),
        ];

        $duplicateField = null;
        $duplicateValue = null;
        $firstSeenRow = null;
        $duplicateSource = null;
        $existingRecord = null;

        // Check database duplicates first
        foreach ($checks as $field => $value) {
            if (!$value) continue;

            // Map field to database record key
            $dbKey = $this->mapFieldToDbKey($field);
            
            if (isset($existingDbRecords[$dbKey][$value])) {
                $duplicateField = $field;
                $duplicateValue = $value;
                $duplicateSource = 'database';
                $existingRecord = $existingDbRecords[$dbKey][$value];
                $firstSeenRow = 'Exists in System';
                break;
            }
        }
        
        // If not duplicate in database, check within file duplicates
        if (!$duplicateField) {
            foreach ($checks as $field => $value) {
                if (!$value) continue;
                
                // Check if this value appears more than once in the file
                if (isset($allFileValues[$field][$value]) && count($allFileValues[$field][$value]) > 1) {
                    $duplicateField = $field;
                    $duplicateValue = $value;
                    $duplicateSource = 'file';
                    // Find the first occurrence row
                    $firstSeenRow = min($allFileValues[$field][$value]);
                    break;
                }
            }
        }

        // If duplicate found (including first occurrences of values that appear elsewhere)
        if ($duplicateField !== null) {
            $duplicateInfo = [
                'row' => $excelRow,
                'empId' => $checks['empId'],
                'accno' => $checks['accno'],
                'kra' => $checks['kra'],
                'email' => $checks['email'],
                'duplicate_field' => $duplicateField,
                'duplicate_value' => $duplicateValue,
                'first_seen_row' => $firstSeenRow,
                'duplicate_source' => $duplicateSource,
            ];
            
            // Add database record info if applicable
            if ($duplicateSource === 'database' && $existingRecord) {
                $duplicateInfo['existing_record'] = $existingRecord;
            }
            
            $duplicateRows[$rowIndex] = $duplicateInfo;
        }
    }

    return $duplicateRows;
}

private function mapFieldToDbKey(string $field): string
{
    return match($field) {
        'empId' => 'emp_ids',
        'email' => 'emails',
        'accno' => 'accnos',
        'kra' => 'kras',
        default => '',
    };
}

private function getExistingDatabaseRecordsWithDetails(array $rows): array
{
    $empIds = array_unique(array_filter(array_map(fn($row) => $this->getCellValue($row, 0), $rows)));
    $emails = array_unique(array_filter(array_map(fn($row) => $this->getCellValue($row, 7), $rows)));
    $accnos = array_unique(array_filter(array_map(fn($row) => $this->getCellValue($row, 5), $rows)));
    $kras = array_unique(array_filter(array_map(fn($row) => $this->getCellValue($row, 6), $rows)));
    
    $result = [
        'emp_ids' => [],
        'emails' => [],
        'accnos' => [],
        'kras' => [],
    ];
    
    // Get agents with their details
    if (!empty($empIds) || !empty($emails)) {
        $agents = Agents::where(function($q) use ($empIds, $emails) {
            if (!empty($empIds)) $q->whereIn('emp_id', $empIds);
            if (!empty($emails)) $q->orWhereIn('EmailId', $emails);
        })->get(['emp_id', 'EmailId', 'FirstName', 'LastName']);
        
        foreach ($agents as $agent) {
            if (!empty($agent->emp_id)) {
                $result['emp_ids'][$agent->emp_id] = [
                    'type' => 'agent',
                    'name' => trim($agent->FirstName . ' ' . $agent->LastName),
                    'email' => $agent->EmailId,
                ];
            }
            if (!empty($agent->EmailId)) {
                $result['emails'][$agent->EmailId] = [
                    'type' => 'agent',
                    'emp_id' => $agent->emp_id,
                    'name' => trim($agent->FirstName . ' ' . $agent->LastName),
                ];
            }
        }
    }
    
    // Get registrations with their details
    if (!empty($accnos) || !empty($kras)) {
        $registrations = Registration::where(function($q) use ($accnos, $kras) {
            if (!empty($accnos)) $q->whereIn('AccountNo', $accnos);
            if (!empty($kras)) $q->orWhereIn('kra', $kras);
        })->get(['empid', 'AccountNo', 'kra']);
        
        foreach ($registrations as $reg) {
            if (!empty($reg->AccountNo)) {
                $result['accnos'][$reg->AccountNo] = [
                    'type' => 'registration',
                    'empid' => $reg->empid,
                ];
            }
            if (!empty($reg->kra)) {
                $result['kras'][$reg->kra] = [
                    'type' => 'registration',
                    'empid' => $reg->empid,
                ];
            }
        }
    }
    
    return $result;
}
private function generateDuplicateReport(array $duplicateRows): string
{
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Duplicate Exceptions');

    // Header row
    $headers = [
        'A1' => 'Row #',
        'B1' => 'Agent ID',
        'C1' => 'Account No',
        'D1' => 'KRA PIN',
        'E1' => 'Email',
        'F1' => 'Duplicate Field',
        'G1' => 'Duplicate Value',
        'H1' => 'First Seen at Row',
    ];

    foreach ($headers as $cell => $label) {
        $sheet->setCellValue($cell, $label);
        $sheet->getStyle($cell)->getFont()->setBold(true);
        $sheet->getStyle($cell)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('1a56db');
        $sheet->getStyle($cell)->getFont()->getColor()
            ->setRGB('FFFFFF');
    }

    // Data rows
    $r = 2;
    foreach ($duplicateRows as $dup) {
        $sheet->setCellValue("A{$r}", $dup['row']);
        $sheet->setCellValue("B{$r}", $dup['empId']   ?? '');
        $sheet->setCellValue("C{$r}", $dup['accno']   ?? '');
        $sheet->setCellValue("D{$r}", $dup['kra']     ?? '');
        $sheet->setCellValue("E{$r}", $dup['email']   ?? '');
        $sheet->setCellValue("F{$r}", $dup['duplicate_field']);
        $sheet->setCellValue("G{$r}", $dup['duplicate_value']);
        $sheet->setCellValue("H{$r}", $dup['first_seen_row']);

        // Highlight duplicate field column red
        $sheet->getStyle("F{$r}")->getFont()->getColor()->setRGB('dc2626');
        $sheet->getStyle("F{$r}")->getFont()->setBold(true);

        $r++;
    }

    // Auto-size columns
    foreach (range('A', 'H') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Save to temp file
    $path = sys_get_temp_dir() . '/import_exceptions_' . time() . '.xlsx';
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save($path);

    return $path;
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