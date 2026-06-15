<?php

namespace App\Http\Controllers;

use App\Models\Banks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

class BanksController extends Controller
{
    public function create()
{
    $banks = Banks::distinct()->get(['ID', 'BankCode', 'Bank','Branch','BranchCode','swiftcode']);
    dd($banks); // Debug data
    return view('students.static', compact('Banks'));
}


    public function store(Request $request)
{
    // Validate the request
    $validator = Validator::make($request->all(), [
        'BankCode' => 'required|string|max:255',
        'Bank' => 'required|string|max:255',
        'Branch' => 'required|string|max:255',
        'BranchCode' => 'required|string|max:255',
        'swiftcode' => 'nullable|string|max:255',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors(),
        ], 422);
    }

    // Insert into the database
    Banks::create([
        'BankCode' => $request->BankCode,
        'Bank' => $request->Bank,
        'Branch' => $request->Branch,
        'BranchCode' => $request->BranchCode,
        'swiftcode' => $request->swiftcode,
    ]);

    return response()->json([
        'message' => 'Bank Saved!',
    ]);
}

public function getAll()
{
    // Join houses with branches to include branchname
    $banks = Banks::paginate(5); // Paginate the results

    return response()->json([
        'data' => $banks->items(),
        'pagination' => [
            'current_page' => $banks->currentPage(),
            'last_page' => $banks->lastPage(),
            'per_page' => $banks->perPage(),
            'total' => $banks->total(),
        ],
    ]);
}

public function getAllBanks()
{
    // Fetch all branches
    $banks = DB::table('banks')
    ->select('Bank')
    ->distinct()
        ->get();

    return response()->json([
        'data' => $banks,
    ]);
}
public function getBranchesDepts()
{
    // Fetch all branches
    $branches = Banks::all();

    return response()->json([
        'data' => $branches,
    ]);
}
public function getBranchesByBank(Request $request) {
    $campusId = $request->input('campusId');
    
    // Fetch classes filtered by campus ID (caid)
    $branches = Banks::where('Bank', $campusId)->get();
    
    return response()->json([
        'data' => $branches,
    ]);
}

public function getCodesBank(Request $request)
{
    $request->validate([
        'bank' => 'required|string',
        'branch' => 'required|string',
    ]);

    $branches = Banks::where('Bank', $request->bank)
        ->where('Branch', $request->branch)
        ->get();

    return response()->json([
        'data' => $branches,
    ]);
}



public function update(Request $request, $id)
{
    $userId = session('user_id') ?? Auth::id();
    
    
    $banks = Banks::findOrFail($id);
    
    $data = $request->validate([
        'bankName' => 'required|string|max:255',
        'bankCode' => 'required|string|max:255',
        'branchName' => 'required|string|max:255',
        'branchCode' => 'required|string|max:255',
        'swiftcode' => 'required|string|max:255',
    ]);

    
    Log::info('Validated data:', $data); // Add logging for debugging
    
    $banks->update($data);
    
    Log::info('After update:', $banks->toArray()); // Add logging for debugging
    
    return response()->json([
        'message' => 'Bank updated successfully',
        'data' => $banks
    ]);
}
/*
public function destroy($id)
{
    $depts = Depts::find($id);

    if (!$depts) {
        return response()->json([
            'success' => false,
            'message' => 'Branch not found.'
        ], 404);
    }

    try {
        $depts->delete();

        return response()->json([
            'success' => true,
            'message' => 'Branch deleted successfully!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to delete branch. Please try again.',
            'error' => $e->getMessage()
        ], 500);
    }
}*/

public function ImportBanks(Request $request)
{
    set_time_limit(300);
    ini_set('max_execution_time', 300);
    $userId = Auth::id();


    if (ob_get_level()) { ob_end_clean(); }

    header('Content-Type: application/json');
    header('X-Accel-Buffering: no');
    header('Cache-Control: no-cache');

    ini_set('output_buffering', 'off');
    ini_set('zlib.output_compression', 'off');
    if (function_exists('apache_setenv')) {
        apache_setenv('no-gzip', '1');
    }

    // ✅ Shared helper — generates ref, logs real error, returns safe message
    $safeError = function(string $context, \Exception $e, array $extra = []): array {
        $ref = strtoupper(substr(md5(uniqid('', true)), 0, 8));
        Log::error($context . ' [Ref: ' . $ref . ']', array_merge([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], $extra));
        return [
            'ref'     => $ref,
            'message' => 'An unexpected error occurred. (Ref: ' . $ref . ')'
        ];
    };

    try {
        $request->validate([
            'excelFile' => 'required|file|mimes:xlsx,xls|max:10240'
        ]);

        $file     = $request->file('excelFile');
        $filePath = $file->getRealPath();
        $ext      = strtolower($file->getClientOriginalExtension());

        $reader      = ($ext === 'xls') ? new Xls() : new Xlsx();
        $spreadsheet = $reader->load($filePath);
        $sheet       = $spreadsheet->getActiveSheet();
        $rows        = $sheet->toArray();

        if (count($rows) <= 1) {
            echo json_encode([
                "status"  => "error",
                "message" => "Excel file appears empty or missing data rows."
            ]);
            return;
        }

        $header       = array_shift($rows);
        $total        = count($rows);
        $current      = 0;
        $successCount = 0;
        $errorCount   = 0;
        $errors       = [];

       

        DB::beginTransaction();

        foreach ($rows as $rowIndex => $row) {
            $current++;

            

            try {
                $bankcode   = $this->getCellValue($row, 0);
                $bank    = $this->getCellValue($row, 1);
                $branch   = $this->getCellValue($row, 2);
                $brcode    = $this->getCellValue($row, 3);
                $swiftcode   = $this->getCellValue($row, 4);
                $dtbcode   = $this->getCellValue($row, 5);
                

                if (!$bankcode) { continue; }

               

                Banks::updateOrCreate(
                    ['BranchCode' => $brcode],
                    [
                        'BankCode'  => $bankcode,
                        'Bank'   => $bank,
                        'Branch'     => $branch,
                        'swiftcode' => $swiftcode,
                        'dtbcode' => $dtbcode,
                    ]
                );

                

                $successCount++;

            } catch (\Exception $e) {
                $errorCount++;

                // ✅ Line 220 fix — ref logged server-side, generic message to client
                $ref = strtoupper(substr(md5(uniqid('', true)), 0, 8));
                Log::error("Import row error [Ref: $ref]", [
                    'row'   => $rowIndex + 2,
                    'error' => $e->getMessage()  // ✅ server log only
                ]);

                // ✅ Client only sees row number and ref — no exception internals
                $errors[] = "Row " . ($rowIndex + 2) . ": Processing failed. (Ref: $ref)";
            }

            if ($current % 50 === 0 || $current === $total) {
                echo json_encode([
                    "status"   => "progress",
                    "progress" => round(($current / $total) * 100),
                    "message"  => "Processed $current of $total rows",
                    "success"  => $successCount,
                    "errors"   => $errorCount
                ]) . "\n";

                if (ob_get_level() > 0) { ob_flush(); }
                flush();
            }
        }

        DB::commit();

Log::info('Parts import completed', [
    'total_rows' => $total,
    'success'    => $successCount,
    'errors'     => $errorCount
]);

$finalMessage = [
    "status"               => "success",
    "message"              => "Import complete. " . (!empty($duplicateRows)
                                ? count($duplicateRows) . " duplicate rows skipped."
                                : ""),
    "total"                => $total,
    "success"              => $successCount,
    "errors"               => $errorCount,
    "has_duplicate_report" => !empty($duplicateRows),
];

if (!empty($errors) && count($errors) <= 10) {
    $finalMessage['error_details'] = $errors;
}

return response()->json($finalMessage);

} catch (\Illuminate\Validation\ValidationException $e) {
    DB::rollBack();

    return response()->json([
        "status"  => "error",
        "message" => "Invalid file upload.",
        "details" => $e->errors()
    ], 422);

} catch (\Exception $e) {
    DB::rollBack();

    $safe = $safeError('Import failed', $e, ['user_id' => $userId]);

    return response()->json([
        "status"    => "error",
        "message"   => "Import failed. Please try again. (Ref: " . $safe['ref'] . ")",
        "reference" => $safe['ref']
    ], 500);
}
}


    /**
     * Get cell value helper
     */
    private function getCellValue($row, $index)
    {
        return isset($row[$index]) && trim($row[$index]) !== '' ? trim($row[$index]) : null;
    }


    public function downloadTemplate()
{
    $headers = [
        'Bank Code',
        'Bank Name', 
        'Branch Name',
        'EFT Code',
        'RTGS Code',
        'DTB Code'
    ];

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Alternative approach: Set headers using fromArray() method
    $sheet->fromArray($headers, null, 'A1');

    // Add sample data
    $sampleData = [
        ['01', 'Kenya Commercial Bank Limited ', 'Eastleigh ', '01091', 'KCBLKENXDMM', ''],
        ['63', 'Diamond Trust Bank Limited', 'Kitengela ', '63025', 'DTKEKENA', 'KGB']
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
    $fileName = 'banks_import_template_' . date('Y-m-d') . '.xlsx';
    $tempFile = tempnam(sys_get_temp_dir(), 'excel') . '.xlsx';
    
    $writer->save($tempFile);

    return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
}

}

