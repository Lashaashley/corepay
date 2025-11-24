<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Payhouse;
use App\Models\Agents;
use App\Models\Registration;
use App\Models\Structure;

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

        // Create headers
        $headers = array_merge(['WorkNo', 'NAME'], $paymentBenefitColumns, ['GROSS'], $deductionColumns, ['TOT_DED', 'NET']);

        // Create PDF
        $pdf = new PayrollSummaryPDF('L', 'mm', 'A3', $this->schoolDetails, $headers, "$month $year");
        $pdf->AliasNbPages();
        $pdf->AddPage();

        // Add data to PDF
        $this->addDataToPdf($pdf, $reportData, $headers);

        return $pdf->Output('S');
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
            DB::raw("CONCAT(e.FirstName, ' ', e.LastName) AS NAME"),
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

    return $query
        ->groupBy('e.emp_id', 'e.FirstName', 'e.LastName')
        ->orderBy('e.emp_id')
        ->get()
        ->toArray();
}


    /**
     * Add data to PDF
     */
    private function addDataToPdf($pdf, array $reportData, array $headers): void
    {
        $totals = array_fill(0, count($headers), 0);
        $rowCount = 0;

        $pdf->SetFont('Arial', '', 7);
        
        foreach ($reportData as $row) {
            $rowCount++;
            
            foreach ($headers as $i => $header) {
                $value = $row[$header] ?? '';
                $width = ($header === 'NAME') ? 40 : 18;
                $align = 'R';

                if ($header === 'WorkNo') {
                    $pdf->Cell($width, 6, $value, 1, 0, $align);
                } else {
                    if (is_numeric($value)) {
                        $totals[$i] += $value;
                        $value = number_format($value, 2);
                    }
                    $pdf->Cell($width, 6, $value, 1, 0, $align);
                }
            }
            $pdf->Ln();
        }

        // Add totals row
        $pdf->AddTotals($totals, $rowCount);
        
        // Add signatures
        $pdf->AddSignatures();
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
    }
}