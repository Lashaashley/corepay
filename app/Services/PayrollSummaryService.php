<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Payhouse;
use App\Models\Agents;
use App\Models\Registration;
use App\Models\Structure;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class PayrollSummaryService
{
    protected $schoolDetails;

    public function __construct()
    {
        $this->schoolDetails = Structure::first();
    }

    /**
     * Generate payroll summary report
     */
    public function generatePayrollSummary(string $month, string $year, ?string $staff3, ?string $staff4): string
    {
        // Include FPDF
        if (!class_exists('FPDF')) {
            require_once base_path('fpdf/fpdf.php');
        }

        $allowedPayrollIds = session('allowedPayroll', []);
        
        if (empty($allowedPayrollIds)) {
            throw new \Exception('No payroll access granted');
        }

        // Get dynamic columns
        $columnsData = $this->getDynamicColumns($month, $year);
        $columns = $columnsData['all_columns'];
        $paymentBenefitColumns = $columnsData['payment_benefit_columns'];
        $deductionColumns = $columnsData['deduction_columns'];

        // Fetch report data
        $reportData = $this->fetchPayrollSummaryData($month, $year, $staff3, $staff4, $allowedPayrollIds, $columns);

        $workNos = array_column($reportData, 'WorkNo');           // NEW
        $backlogSummary = $this->getBacklogSummary($workNos, $month, $year); // NEW

        // Create headers
        $headers = array_merge(['WorkNo', 'NAME'], $paymentBenefitColumns, ['GROSS'], $deductionColumns, ['TOT_DED', 'NET']);

        // Create PDF
        $pdf = new PayrollSummaryPDF('L', 'mm', 'A3', $this->schoolDetails, $headers, "$month $year");
        $pdf->AliasNbPages();
        $pdf->AddPage();

        // Add data to PDF
        $this->addDataToPdf($pdf, $reportData, $headers, $backlogSummary);

        return $pdf->Output('S');
    }
    public function generatePayrollSummaryExcel(string $month, string $year, ?string $staff3, ?string $staff4): string
    {
        $allowedPayrollIds = session('allowedPayroll', []);
        
        if (empty($allowedPayrollIds)) {
            throw new \Exception('No payroll access granted');
        }

        // Get dynamic columns
        $columnsData = $this->getDynamicColumns($month, $year);
        $columns = $columnsData['all_columns'];
        $paymentBenefitColumns = $columnsData['payment_benefit_columns'];
        $deductionColumns = $columnsData['deduction_columns'];

        // Fetch report data
        $reportData = $this->fetchPayrollSummaryData($month, $year, $staff3, $staff4, $allowedPayrollIds, $columns);

        $workNos = array_column($reportData, 'WorkNo');
        $backlogSummary = $this->getBacklogSummary($workNos, $month, $year);
        $statusTotals = $this->computeCurrentPeriodStatusTotals($reportData);

        // Create headers
        $headers = array_merge(['WorkNo', 'NAME'], $paymentBenefitColumns, ['GROSS'], $deductionColumns, ['TOT_DED', 'NET']);

        // Create Excel file
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set sheet name
        $sheet->setTitle('Payroll Summary');

        // Add title
        $this->addExcelTitle($sheet, $month, $year);
        
        // Add headers
        $this->addExcelHeaders($sheet, $headers);
        
        // Add data
        $this->addExcelData($sheet, $reportData, $headers);
        
        // Add totals
        $this->addExcelTotals($sheet, $reportData, $headers);
        
        // Add signatures section
        $nextRow = $this->addExcelPaymentStatusSummary($sheet, 7 + count($reportData), $statusTotals, $backlogSummary);
       // $this->addExcelSignatures($sheet, count($reportData));
        
        // Auto-size columns
        $this->autoSizeColumns($sheet, count($headers));

        // Generate file
        $writer = new Xlsx($spreadsheet);
        
        // Save to temp file and return content
        $tempFile = tempnam(sys_get_temp_dir(), 'payroll_summary_');
        $writer->save($tempFile);
        $content = file_get_contents($tempFile);
        unlink($tempFile);
        
        return $content;
    }
  /*  private function addExcelSignatures($sheet, int $dataRowCount): void
    {
        $startRow = 7 + $dataRowCount;
        
        $sheet->setCellValue('A' . $startRow, 'Prepared By');
        $sheet->setCellValue('B' . $startRow, 'Date');
        $sheet->mergeCells('A' . $startRow . ':A' . ($startRow + 1));
        $sheet->mergeCells('B' . $startRow . ':B' . ($startRow + 1));
        
        $startRow += 2;
        $sheet->setCellValue('A' . $startRow, 'Checked By');
        $sheet->setCellValue('B' . $startRow, 'Date');
        $sheet->mergeCells('A' . $startRow . ':A' . ($startRow + 1));
        $sheet->mergeCells('B' . $startRow . ':B' . ($startRow + 1));
        
        $startRow += 2;
        $sheet->setCellValue('A' . $startRow, 'Authorised By');
        $sheet->setCellValue('B' . $startRow, 'Date');
        $sheet->mergeCells('A' . $startRow . ':A' . ($startRow + 1));
        $sheet->mergeCells('B' . $startRow . ':B' . ($startRow + 1));
        
        // Style signature section
        $lastRow = $startRow + 1;
        $sheet->getStyle('A' . ($startRow - 4) . ':B' . $lastRow)->applyFromArray([
            'font' => ['bold' => true],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
    }*/

    /**
     * Auto-size columns
     */
    private function autoSizeColumns($sheet, int $columnCount): void
    {
        for ($i = 0; $i < $columnCount; $i++) {
            $column = chr(ord('A') + $i);
            
            if ($column === 'B') { // NAME column
                $sheet->getColumnDimension($column)->setWidth(30);
            } else {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
        }
    }

    /**
     * Add title to Excel
     */
    private function addExcelTitle($sheet, string $month, string $year): void
    {
        // School name
        $sheet->setCellValue('A1', $this->schoolDetails->name ?? 'School Name');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Report title
        $sheet->setCellValue('A2', "Payroll Summary for {$month} {$year}");
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
        
        // Add spacing
        $sheet->getRowDimension(3)->setRowHeight(5);
    }

    /**
     * Add headers to Excel
     */
    private function addExcelHeaders($sheet, array $headers): void
    {
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '4', $header);
            $column++;
        }
        
        // Style headers
        $lastColumn = chr(ord('A') + count($headers) - 1);
        $sheet->getStyle('A4:' . $lastColumn . '4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
    }

    /**
     * Add data rows to Excel
     */
    private function addExcelData($sheet, array $reportData, array $headers): void
{
    $row = 5;

    foreach ($reportData as $data) {
        $status = $data['invoice_status'] ?? 'NOT PAID';
        $hasBacklog = $data['has_backlog'] ?? false;

        $fillColor = null;
        if ($status === 'NOT PAID') {
            $fillColor = 'FFC7CE'; // light red — not invoiced this period
        } elseif ($hasBacklog) {
            $fillColor = 'FFEB9C'; // light amber — also carrying backlog from another period
        }

        foreach ($headers as $i => $header) {
            $column = Coordinate::stringFromColumnIndex($i + 1); // NEW: safe past column Z
            $value = $data[$header] ?? '';

            if ($header === 'NAME' && $hasBacklog) {
                $value .= ' *(' . $data['backlog_label'] . ')'; // NEW: indicates which period(s)
            }

            if ($header === 'WorkNo' || $header === 'NAME') {
                $sheet->setCellValue($column . $row, $value);
            } else {
                $sheet->setCellValue($column . $row, is_numeric($value) ? (float) $value : 0);
                $sheet->getStyle($column . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            }

            if ($fillColor) {
                $sheet->getStyle($column . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB($fillColor);
            }
        }

        $row++;
    }

    $lastColumn = Coordinate::stringFromColumnIndex(count($headers));
    $lastRow = $row - 1;
    $sheet->getStyle('A5:' . $lastColumn . $lastRow)->applyFromArray([
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]]
    ]);
}

    /**
     * Add totals row to Excel
     */
    private function addExcelTotals($sheet, array $reportData, array $headers): void
    {
        $row = 5 + count($reportData);
        $totals = array_fill(0, count($headers), 0);
        
        // Calculate totals
        foreach ($reportData as $data) {
            foreach ($headers as $i => $header) {
                if ($i > 1 && is_numeric($data[$header] ?? 0)) { // Skip WorkNo and NAME
                    $totals[$i] += $data[$header];
                }
            }
        }
        
        // Add totals row
        $column = 'A';
        foreach ($headers as $i => $header) {
            if ($i === 0) {
                $sheet->setCellValue($column . $row, 'Total');
            } elseif ($i === 1) {
                $sheet->setCellValue($column . $row, count($reportData));
            } else {
                $sheet->setCellValue($column . $row, $totals[$i]);
                $sheet->getStyle($column . $row)->getNumberFormat()
                    ->setFormatCode('#,##0.00');
            }
            $column++;
        }
        
        // Style totals row
        $lastColumn = chr(ord('A') + count($headers) - 1);
        $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E7E6E6']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
    }

    /**
     * Get dynamic columns from payhouse table
     */
    private function getDynamicColumns(string $month, string $year): array
    {
        $columns = Payhouse::whereIn('pcategory', ['Basic', 'Payment', 'Benefit', 'Deduction'])
            ->where('month', $month)
            ->where('year', $year)
            ->select('pname', 'pcategory')
            ->distinct()
            ->orderByRaw("
                CASE 
                    WHEN pname = 'Basic Salary' THEN 1
                    WHEN pcategory = 'Payment' THEN 2
                    WHEN pcategory = 'Benefit' THEN 3
                    WHEN pname = 'PAYE' THEN 5
                    WHEN pcategory = 'Deduction' THEN 6
                    WHEN pname = 'NET PAY' THEN 7
                END
            ")
            ->get();

        $paymentBenefitColumns = [];
        $deductionColumns = [];
        $allColumns = [];

        foreach ($columns as $column) {
            if (in_array($column->pcategory, ['Basic', 'Payment', 'Benefit'])) {
                $paymentBenefitColumns[] = $column->pname;
            } else {
                $deductionColumns[] = $column->pname;
            }
            $allColumns[] = $column->pname;
        }

        return [
            'all_columns' => $allColumns,
            'payment_benefit_columns' => $paymentBenefitColumns,
            'deduction_columns' => $deductionColumns
        ];
    }

    /**
 * For a given set of WorkNos: current-period invoice status, and any
 * backlog (prior-period debt that's either still TO BE PAID, or was
 * PAID in this exact run via paid_atmonth/paid_atyear).
 */
private function getPaymentStatusData(array $workNos, string $month, string $year): array
{
    $current = \App\Models\PaymentStatus::where('month', $month)
        ->where('year', $year)
        ->whereIn('WorkNo', $workNos)
        ->pluck('status', 'WorkNo');

    $backlog = \App\Models\PaymentStatus::whereIn('WorkNo', $workNos)
        ->where(function ($q) use ($month, $year) {
            $q->where('status', 'TO BE PAID')
              ->orWhere(function ($q2) use ($month, $year) {
                  $q2->where('status', 'PAID')
                     ->where('paid_atmonth', $month)
                     ->where('paid_atyear', $year);
              });
        })
        ->where(function ($q) use ($month, $year) {
            $q->where('month', '!=', $month)->orWhere('year', '!=', $year);
        })
        ->get()
        ->groupBy('WorkNo');

    return ['current' => $current, 'backlog' => $backlog];
}

    /**
     * Fetch payroll summary data
     */
    private function fetchPayrollSummaryData(string $month, string $year, ?string $staff3, ?string $staff4, array $allowedPayrollIds, array $columns): array
{
    // Build CASE statements safely
    $caseStatements = '';
    foreach ($columns as $column) {
        $safeColumn = str_replace('`', '', $column); // prevent accidental backtick issues
        $caseStatements .= "MAX(CASE WHEN p.pname = '{$safeColumn}' THEN p.tamount ELSE 0 END) AS `{$safeColumn}`,"; 
    }
    $caseStatements = rtrim($caseStatements, ','); // FIX trailing comma

    $query = Agents::from('tblemployees as e')
        ->select(
            'e.emp_id AS WorkNo',
            DB::raw("TRIM(CONCAT(COALESCE(e.FirstName, ''), ' ', COALESCE(e.LastName, ''))) AS NAME"),
            DB::raw($caseStatements),
            DB::raw("SUM(CASE WHEN p.pcategory IN ('Basic','Payment','Benefit') THEN p.tamount ELSE 0 END) AS GROSS"),
            DB::raw("SUM(CASE WHEN p.pcategory = 'Deduction' OR p.pname = 'PAYE' THEN p.tamount ELSE 0 END) AS TOT_DED"),
            DB::raw("SUM(CASE WHEN p.pname = 'NET PAY' THEN p.tamount ELSE 0 END) AS NET")
        )
        ->leftJoin('payhouse as p', 'e.emp_id', '=', 'p.WorkNo')
        ->join('registration as r', 'e.emp_id', '=', 'r.empid')
        ->where('p.month', '=', (string) $month)
        ->where('p.year', '=', (string) $year)
        ->whereIn('r.payrolty', $allowedPayrollIds);

    // Staff range filter
    if ($staff3 && $staff4) {
        $query->whereBetween('e.emp_id', [$staff3, $staff4]);
    } elseif ($staff3) {
        $query->where('e.emp_id', '>=', $staff3);
    } elseif ($staff4) {
        $query->where('e.emp_id', '<=', $staff4);
    }
$reportData = $query
    ->groupBy('e.emp_id', 'e.FirstName', 'e.LastName')
    ->orderBy('e.emp_id')
    ->get()
    ->toArray();

if (empty($reportData)) {
    return $reportData;
}

// NEW: attach invoice status + backlog flag per row
$workNos = array_column($reportData, 'WorkNo');
$statusData = $this->getPaymentStatusData($workNos, $month, $year);

foreach ($reportData as &$row) {
    $row['invoice_status'] = $statusData['current'][$row['WorkNo']] ?? 'NOT PAID';
    $backlogRows = $statusData['backlog']->get($row['WorkNo']);
    $row['has_backlog'] = $backlogRows && $backlogRows->count() > 0;
    $row['backlog_label'] = $row['has_backlog']
        ? $backlogRows->map(fn($b) => $b->month . ' ' . $b->year)->implode(', ')
        : null;
}
unset($row);

return $reportData;
}

private function computeCurrentPeriodStatusTotals(array $reportData): array
{
    $invoicedTotal = 0; $invoicedCount = 0;
    $notInvoicedTotal = 0; $notInvoicedCount = 0;

    foreach ($reportData as $row) {
        $net = (float) ($row['NET'] ?? 0);
        if (($row['invoice_status'] ?? 'NOT PAID') === 'NOT PAID') {
            $notInvoicedTotal += $net;
            $notInvoicedCount++;
        } else {
            $invoicedTotal += $net;
            $invoicedCount++;
        }
    }

    return compact('invoicedTotal', 'invoicedCount', 'notInvoicedTotal', 'notInvoicedCount');
}

/**
 * Totals only (for the summary section) — scoped to the same WorkNos
 * already filtered by staff range in the main query, so this respects
 * the same $staff3/$staff4 window the report is showing.
 */
private function getBacklogSummary(array $workNos, string $month, string $year): array
{
    $rows = \App\Models\PaymentStatus::join('payhouse', function ($join) {
            $join->on(DB::raw('payment_status.WorkNo COLLATE utf8mb4_general_ci'), '=', DB::raw('payhouse.WorkNo COLLATE utf8mb4_general_ci'))
                 ->on('payment_status.month', '=', 'payhouse.month')
                 ->on('payment_status.year', '=', 'payhouse.year');
        })
        ->whereIn('payment_status.WorkNo', $workNos)
        ->where('payhouse.itemcode', 'P99')
        ->where(function ($q) use ($month, $year) {
            $q->where('payment_status.status', 'TO BE PAID')
              ->orWhere(function ($q2) use ($month, $year) {
                  $q2->where('payment_status.status', 'PAID')
                     ->where('payment_status.paid_atmonth', $month)
                     ->where('payment_status.paid_atyear', $year);
              });
        })
        ->where(function ($q) use ($month, $year) {
            $q->where('payment_status.month', '!=', $month)
              ->orWhere('payment_status.year', '!=', $year);
        })
        ->selectRaw('payment_status.status, SUM(payhouse.tamount) as total, COUNT(DISTINCT payment_status.WorkNo) as employee_count')
        ->groupBy('payment_status.status')
        ->get();

    $paidTotal = (float) ($rows->firstWhere('status', 'PAID')->total ?? 0);
    $paidCount = (int) ($rows->firstWhere('status', 'PAID')->employee_count ?? 0);
    $outstandingTotal = (float) ($rows->firstWhere('status', 'TO BE PAID')->total ?? 0);
    $outstandingCount = (int) ($rows->firstWhere('status', 'TO BE PAID')->employee_count ?? 0);

    return compact('paidTotal', 'paidCount', 'outstandingTotal', 'outstandingCount');
}


    /**
     * Add data to PDF
     */
    private function addDataToPdf($pdf, array $reportData, array $headers, array $backlogSummary): void
{
    $totals = array_fill(0, count($headers), 0);
    $rowCount = 0;

    // NEW: current-period summary accumulators
    $invoicedTotal = 0; $invoicedCount = 0;
    $notInvoicedTotal = 0; $notInvoicedCount = 0;

    $pdf->SetFont('Arial', '', 7);

    foreach ($reportData as $row) {
        $rowCount++;

        $netPay = (float) ($row['NET'] ?? 0);
        $status = $row['invoice_status'] ?? 'NOT PAID';
        $hasBacklog = $row['has_backlog'] ?? false;
        $fill = false;

        if ($status === 'NOT PAID') {
            $pdf->SetFillColor(255, 199, 206); // light red — not invoiced this period
            $fill = true;
            $notInvoicedTotal += $netPay;
            $notInvoicedCount++;
        } else {
            $invoicedTotal += $netPay;
            $invoicedCount++;
            if ($hasBacklog) {
                $pdf->SetFillColor(255, 235, 156); // light amber — also carrying backlog from another period
                $fill = true;
            }
        }

        foreach ($headers as $i => $header) {
            $value = $row[$header] ?? '';
            $width = ($header === 'NAME') ? 40 : 18;
            $align = 'R';

            // NEW: indicate which period(s) the backlog belongs to
            if ($header === 'NAME' && $hasBacklog) {
                $value .= ' *(' . $row['backlog_label'] . ')';
            }

            if (is_numeric($value) && $header !== 'WorkNo') {
                $totals[$i] += $value;
                $value = number_format($value, 2);
            }
            $pdf->Cell($width, 6, $value, 1, 0, $align, $fill);
        }
        $pdf->Ln();
    }

    $pdf->AddTotals($totals, $rowCount);

    // NEW: payment status summary block
    $pdf->AddPaymentStatusSummary([
        'invoiced_total' => $invoicedTotal,
        'invoiced_count' => $invoicedCount,
        'not_invoiced_total' => $notInvoicedTotal,
        'not_invoiced_count' => $notInvoicedCount,
        'backlog_paid_total' => $backlogSummary['paidTotal'],
        'backlog_paid_count' => $backlogSummary['paidCount'],
        'backlog_outstanding_total' => $backlogSummary['outstandingTotal'],
        'backlog_outstanding_count' => $backlogSummary['outstandingCount'],
    ]);

    $pdf->AddSignatures();
}

private function addExcelPaymentStatusSummary($sheet, int $startRow, array $statusTotals, array $backlogSummary): int
{
    $totalPayrun = $statusTotals['invoicedTotal'] + $backlogSummary['paidTotal'] + $backlogSummary['outstandingTotal'];
    $row = $startRow;

    $sheet->setCellValue('A' . $row, 'PAYMENT STATUS SUMMARY');
    $sheet->getStyle('A' . $row)->applyFromArray(['font' => ['bold' => true, 'size' => 12]]);
    $row += 2;

    $sheet->setCellValue('A' . $row, 'Description');
    $sheet->setCellValue('B' . $row, 'Amount');
    $sheet->setCellValue('C' . $row, 'Employees');
    $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
    ]);
    $row++;

    $lines = [
        ['Invoiced Net Pay (Current)', $statusTotals['invoicedTotal'], $statusTotals['invoicedCount'], 'C6EFCE'],
        ['Not Invoiced (Current)', $statusTotals['notInvoicedTotal'], $statusTotals['notInvoicedCount'], 'FFC7CE'],
        ['Backlog Paid This Run (prior periods)', $backlogSummary['paidTotal'], $backlogSummary['paidCount'], 'E2EFDA'],
        ['Backlog Still Outstanding (prior periods)', $backlogSummary['outstandingTotal'], $backlogSummary['outstandingCount'], 'FFEB9C'],
    ];

    foreach ($lines as [$label, $amount, $count, $color]) {
        $sheet->setCellValue('A' . $row, $label);
        $sheet->setCellValue('B' . $row, (float) $amount);
        $sheet->setCellValue('C' . $row, $count);
        $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color]],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
        ]);
        $row++;
    }

    $sheet->setCellValue('A' . $row, 'TOTAL FOR THIS PAYRUN');
    $sheet->setCellValue('B' . $row, (float) $totalPayrun);
    $sheet->setCellValue('C' . $row, $statusTotals['invoicedCount'] + $backlogSummary['paidCount'] + $backlogSummary['outstandingCount']);
    $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
    $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray([
        'font' => ['bold' => true],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '9BD2A5']],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']]]
    ]);

    return $row + 2; // blank spacing row before whatever comes next
}
}

if (!class_exists('PayrollSummaryPDF')) {
    class PayrollSummaryPDF extends \FPDF
    {
        private $schoolDetails;
        private $headers;
        private $period;

        public function __construct($orientation, $unit, $size, $schoolDetails, $headers, $period)
        {
            parent::__construct($orientation, $unit, $size);
            $this->schoolDetails = $schoolDetails;
            $this->headers = $headers;
            $this->period = $period;
        }

        // Header
        public function Header()
        {
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10, $this->schoolDetails->name ?? 'School Name', 0, 1, 'C');
            
            $this->SetFont('Arial', 'I', 10);
            $this->Cell(0, 5, "Payroll Summary for {$this->period}", 0, 1, 'C');
            $this->Ln(5);

            $this->SetFont('Arial', 'B', 8);
            foreach ($this->headers as $header) {
                $width = ($header === 'NAME') ? 40 : 18;
                $truncatedHeader = mb_strlen($header) > ($width / 3) ? mb_substr($header, 0, floor($width / 3)) : $header;
                $this->Cell($width, 7, $truncatedHeader, 1, 0, 'C');
            }
            
            $this->Ln();
        }

        // Footer
        public function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
        }

        // Function to add totals row
        public function AddTotals($totals, $rowCount)
        {
            $this->SetFont('Arial', 'B', 7);
            foreach ($totals as $i => $total) {
                $width = ($i === 1) ? 40 : 18; // Wider width for NAME column
                
                if ($i === 0) {
                    // First column (WorkNo) - display 'Total'
                    $this->Cell($width, 6, 'Total', 1, 0, 'R');
                } elseif ($i === 1) {
                    // Second column (NAME) - show row count
                    $this->Cell($width, 6, $rowCount, 1, 0, 'R');
                } else {
                    // Other columns - show totals
                    $this->Cell($width, 6, number_format($total, 2), 1, 0, 'R');
                }
            }
            $this->Ln();
        }

        // Function to add the signature section
        public function AddSignatures()
        {
            $this->Ln(10);

            $this->SetFont('Arial', 'B', 9);
            $this->Cell(60, 10, 'Prepared By', 1, 0, 'L');
            $this->Cell(60, 10, 'Date', 1, 1, 'L');

            $this->Cell(60, 10, 'Checked By', 1, 0, 'L');
            $this->Cell(60, 10, 'Date', 1, 1, 'L');

            $this->Cell(60, 10, 'Authorised By', 1, 0, 'L');
            $this->Cell(60, 10, 'Date', 1, 1, 'L');
        }

        public function AddPaymentStatusSummary(array $s)
{
    $totalPayrun = $s['invoiced_total'] + $s['backlog_paid_total'] + $s['backlog_outstanding_total'];

    $this->Ln(6);
    $this->SetFont('Arial', 'B', 10);
    $this->Cell(0, 7, 'PAYMENT STATUS SUMMARY', 0, 1, 'L');

    $this->SetFont('Arial', 'B', 8);
    $this->SetFillColor(230, 230, 230);
    $this->Cell(90, 6, 'Description', 1, 0, 'C', true);
    $this->Cell(40, 6, 'Amount', 1, 0, 'C', true);
    $this->Cell(30, 6, 'Employees', 1, 1, 'C', true);

    $this->SetFont('Arial', 'B', 8);
    $this->SetFillColor(198, 239, 206); $this->SetTextColor(0, 97, 0);
    $this->Cell(90, 6, 'Invoiced Net Pay (Current)', 1, 0, 'L', true);
    $this->Cell(40, 6, number_format($s['invoiced_total'], 2), 1, 0, 'R', true);
    $this->Cell(30, 6, $s['invoiced_count'], 1, 1, 'C', true);
    $this->SetTextColor(0, 0, 0);

    $this->SetFillColor(255, 199, 206); $this->SetTextColor(156, 0, 6);
    $this->Cell(90, 6, 'Not Invoiced (Current)', 1, 0, 'L', true);
    $this->Cell(40, 6, number_format($s['not_invoiced_total'], 2), 1, 0, 'R', true);
    $this->Cell(30, 6, $s['not_invoiced_count'], 1, 1, 'C', true);
    $this->SetTextColor(0, 0, 0);

    $this->SetFillColor(226, 239, 218); $this->SetTextColor(0, 97, 0);
    $this->Cell(90, 6, 'Backlog Paid This Run (prior periods)', 1, 0, 'L', true);
    $this->Cell(40, 6, number_format($s['backlog_paid_total'], 2), 1, 0, 'R', true);
    $this->Cell(30, 6, $s['backlog_paid_count'], 1, 1, 'C', true);
    $this->SetTextColor(0, 0, 0);

    $this->SetFillColor(255, 235, 156); $this->SetTextColor(153, 102, 0);
    $this->Cell(90, 6, 'Backlog Still Outstanding (prior periods)', 1, 0, 'L', true);
    $this->Cell(40, 6, number_format($s['backlog_outstanding_total'], 2), 1, 0, 'R', true);
    $this->Cell(30, 6, $s['backlog_outstanding_count'], 1, 1, 'C', true);
    $this->SetTextColor(0, 0, 0);

    $this->SetFont('Arial', 'B', 9);
    $this->SetFillColor(155, 210, 165); $this->SetTextColor(0, 60, 0);
    $this->Cell(90, 7, 'TOTAL FOR THIS PAYRUN', 1, 0, 'L', true);
    $this->Cell(40, 7, number_format($totalPayrun, 2), 1, 0, 'R', true);
    $this->Cell(30, 7, $s['invoiced_count'] + $s['backlog_paid_count'] + $s['backlog_outstanding_count'], 1, 1, 'C', true);
    $this->SetTextColor(0, 0, 0);
}
    }
}