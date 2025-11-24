<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Payhouse;
use App\Models\Registration;
use App\Models\Structure;
use App\Models\OtRecord;

class OverallSummaryService
{
    protected $schoolDetails;
    protected $logoPath;

    public function __construct()
    {
        $this->schoolDetails = Structure::first();
        $this->setLogoPath();
    }

    /**
     * Generate overall payroll summary PDF
     */
    public function generateOverallSummary(string $month, string $year): string
    {
        // Include FPDF
        if (!class_exists('FPDF')) {
            require_once base_path('fpdf/fpdf.php');
        }

        $allowedPayroll = session('allowedPayroll', []);
        
        if (empty($allowedPayroll)) {
            throw new \Exception('Unauthorized access');
        }

        // Fetch payslip data with payroll type filtering
        $payslipData = $this->getPayslipData($month, $year, $allowedPayroll);

        // Categorize records
        $categorizedRecords = $this->categorizeRecords($payslipData);

        // Calculate totals
        $totals = $this->calculateTotals($categorizedRecords);

        // Get paymode summary
        $paymodeSummary = $this->getPaymodeSummary($month, $year, $allowedPayroll);

        // Create PDF
        $pdf = new OverallSummaryPDF('P', 'mm', 'A4', $this->schoolDetails,  $this->logoPath);
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Ln(0);
        $pdf->Cell(0, 8, 'Overall Payslip Report in ' . $month . ' ' . $year, 0, 1, 'C');

        // Add sections
        $this->addGrossSalarySection($pdf, $categorizedRecords['grossSalary'], $month, $year);
        $this->addReliefOnTaxableSection($pdf, $categorizedRecords['reliefOnTaxable']);
        $this->addTaxableIncomeSection($pdf, $totals['taxableIncome']);
        $this->addTaxAndPayeSection($pdf, $categorizedRecords['tax'], $categorizedRecords['reliefOnPaye'], $totals['paye']);
        $this->addDeductionsSection($pdf, $categorizedRecords['deductions'], $month, $year);
        $this->addNetPaySection($pdf, $totals['netPay'], $paymodeSummary['totalNetPayAllModes']);
        $this->addPaymodeSummarySection($pdf, $paymodeSummary);

        return $pdf->Output('S');
    }
    
    

    /**
     * Fetch payslip data with payroll type filtering
     */
    private function getPayslipData(string $month, string $year, array $allowedPayroll)
    {
        return Payhouse::join('registration', 'payhouse.WorkNo', '=', 'registration.empid')
            ->where('payhouse.month', $month)
            ->where('payhouse.year', $year)
            ->whereIn('registration.payrolty', $allowedPayroll)
            ->select(
                'payhouse.ID',
                'payhouse.WorkNo', 
                'payhouse.pname',
                'payhouse.pcategory',
                'payhouse.tamount',
                'payhouse.month',
                'payhouse.year',
                'payhouse.itemcode'
            )
            ->get();
    }

    /**
     * Categorize payslip records
     */
    private function categorizeRecords($payslipData): array
    {
        $categories = [
            'grossSalary' => [],
            'reliefOnTaxable' => [],
            'tax' => [],
            'reliefOnPaye' => [],
            'deductions' => []
        ];

        foreach ($payslipData as $row) {
            switch ($row->pcategory) {
                case 'Payment':
                case 'Benefit':
                    $categories['grossSalary'][] = $row;
                    break;
                case 'RELIEF ON TAXABLE':
                    $categories['reliefOnTaxable'][] = $row;
                    break;
                case 'Tax':
                    $categories['tax'][] = $row;
                    break;
                case 'Relief on Paye':
                    $categories['reliefOnPaye'][] = $row;
                    break;
                case 'Deduction':
                    $categories['deductions'][] = $row;
                    break;
            }
        }

        return $categories;
    }
    private function setLogoPath(): void
    {
        $logoFile = $this->schoolDetails->logo ?? 'default.jpg';
        $this->logoPath = storage_path('app/public/storage/' . $logoFile);
        
        if (!file_exists($this->logoPath)) {
            $this->logoPath = storage_path('app/public/students/default.png');
        }
    }

    /**
     * Calculate totals for each category
     */
    private function calculateTotals(array $categories): array
    {
        $grossSalaryTotal = collect($categories['grossSalary'])->sum('tamount');
        $reliefOnTaxableTotal = collect($categories['reliefOnTaxable'])->sum('tamount');
        $taxTotal = collect($categories['tax'])->sum('tamount');
        $reliefOnPayeTotal = collect($categories['reliefOnPaye'])->sum('tamount');
        $deductionTotal = collect($categories['deductions'])->sum('tamount');

        $taxableIncome = $grossSalaryTotal - $reliefOnTaxableTotal;
        $paye = $taxTotal - $reliefOnPayeTotal;
        $netPay = $grossSalaryTotal - $deductionTotal;

        return [
            'grossSalaryTotal' => $grossSalaryTotal,
            'reliefOnTaxableTotal' => $reliefOnTaxableTotal,
            'taxTotal' => $taxTotal,
            'reliefOnPayeTotal' => $reliefOnPayeTotal,
            'deductionTotal' => $deductionTotal,
            'taxableIncome' => $taxableIncome,
            'paye' => $paye,
            'netPay' => $netPay
        ];
    }

    /**
     * Get paymode summary
     */
    private function getPaymodeSummary(string $month, string $year, array $allowedPayroll): array
    {
        $paymodeData = Payhouse::join('registration', 'payhouse.WorkNo', '=', 'registration.empid')
            ->where('payhouse.pname', 'NET PAY')
            ->where('payhouse.month', $month)
            ->where('payhouse.year', $year)
            ->whereIn('registration.payrolty', $allowedPayroll)
            ->select(
                'registration.paymode',
                DB::raw('SUM(ABS(payhouse.tamount)) as total_net_pay'),
                DB::raw('COUNT(DISTINCT payhouse.WorkNo) as employee_count')
            )
            ->groupBy('registration.paymode')
            ->orderBy('registration.paymode')
            ->get();

        $paymodeSummary = [];
        $totalNetPayAllModes = 0;
        $totalEmployeesAllModes = 0;

        foreach ($paymodeData as $row) {
            $paymodeSummary[] = [
                'paymode' => $row->paymode,
                'total_net_pay' => $row->total_net_pay,
                'employee_count' => $row->employee_count
            ];
            
            $totalNetPayAllModes += $row->total_net_pay;
            $totalEmployeesAllModes += $row->employee_count;
        }

        // Add grand totals
        $paymodeSummary[] = [
            'paymode' => 'GRAND TOTAL',
            'total_net_pay' => $totalNetPayAllModes,
            'employee_count' => $totalEmployeesAllModes
        ];

        return [
            'data' => $paymodeSummary,
            'totalNetPayAllModes' => $totalNetPayAllModes,
            'totalEmployeesAllModes' => $totalEmployeesAllModes
        ];
    }

    /**
     * Add gross salary section
     */
    private function addGrossSalarySection($pdf, $records, $month, $year): void
    {
        $pdf->SectionTitle('Gross Salary');
        $pdf->TableHeader('Description', 'Amount', 'Bal/Qnt');
        
        $grossSalarySums = [];
        $monthNumber = date('n', strtotime("1 $month"));

        // Group by pname and sum amounts
        foreach ($records as $record) {
            if (!isset($grossSalarySums[$record->pname])) {
                $grossSalarySums[$record->pname] = [
                    'total' => 0,
                    'itemcode' => $record->itemcode
                ];
            }
            $grossSalarySums[$record->pname]['total'] += $record->tamount;
        }

        // Get quantities for all item codes at once
        $itemCodes = collect($grossSalarySums)->pluck('itemcode')->filter()->unique()->toArray();
        $quantities = $this->getQuantitiesForItemCodes($itemCodes, $monthNumber, $year);

        // Add rows to PDF
        foreach ($grossSalarySums as $pname => $data) {
            $quantity = isset($quantities[$data['itemcode']]) ? $quantities[$data['itemcode']] : null;
            $pdf->TableRow($pname, $data['total'], $quantity);
        }
        
        $total = collect($grossSalarySums)->sum('total');
        $pdf->TableTotal('Total Gross Salary', $total);
        $pdf->Ln(0);
    }

    /**
     * Get quantities for item codes
     */
    private function getQuantitiesForItemCodes(array $itemCodes, int $monthNumber, string $year): array
    {
        if (empty($itemCodes)) {
            return [];
        }

        return OtRecord::whereIn('Pcode', $itemCodes)
            ->whereYear('odate', $year)
            ->whereMonth('odate', $monthNumber)
            ->groupBy('Pcode')
            ->select('Pcode', DB::raw('COALESCE(SUM(quantity), 0) as quantity'))
            ->get()
            ->keyBy('Pcode')
            ->map(function($item) {
                return $item->quantity;
            })
            ->toArray();
    }

    /**
     * Add relief on taxable section
     */
    private function addReliefOnTaxableSection($pdf, $records): void
    {
        $pdf->SectionTitle('Relief on Taxable');
        $pdf->TableHeader('Description', 'Amount');
        
        $reliefSums = [];
        foreach ($records as $record) {
            if (!isset($reliefSums[$record->pname])) {
                $reliefSums[$record->pname] = 0;
            }
            $reliefSums[$record->pname] += $record->tamount;
        }
        
        foreach ($reliefSums as $pname => $totalAmount) {
            $pdf->TableRow($pname, $totalAmount);
        }
        
        $total = array_sum($reliefSums);
        $pdf->TableTotal('Total Relief on Taxable', $total);
        $pdf->Ln(0);
    }

    /**
     * Add taxable income section
     */
    private function addTaxableIncomeSection($pdf, $taxableIncome): void
    {
        $pdf->TableTotal('Taxable Income', $taxableIncome);
        $pdf->Ln(0);
    }

    /**
     * Add tax and PAYE section
     */
    private function addTaxAndPayeSection($pdf, $taxRecords, $reliefRecords, $paye): void
    {
        $pdf->SectionTitle('Tax and PAYE');
        $pdf->TableHeader('Description', 'Amount');
        
        $taxSums = [];
        foreach ($taxRecords as $record) {
            if (!isset($taxSums[$record->pname])) {
                $taxSums[$record->pname] = 0;
            }
            $taxSums[$record->pname] += $record->tamount;
        }
        
        foreach ($taxSums as $pname => $totalAmount) {
            $pdf->TableRow($pname, $totalAmount);
        }
        
        $reliefSums = [];
        foreach ($reliefRecords as $record) {
            if (!isset($reliefSums[$record->pname])) {
                $reliefSums[$record->pname] = 0;
            }
            $reliefSums[$record->pname] += $record->tamount;
        }
        
        foreach ($reliefSums as $pname => $totalAmount) {
            $pdf->TableRow('  ' . $pname, $totalAmount);
        }
        
        $reliefTotal = array_sum($reliefSums);
        $pdf->TableRow('Relief on PAYE', $reliefTotal);
        $pdf->TableTotal('PAYE', $paye);
        $pdf->Ln(0);
    }

    /**
     * Add deductions section
     */
    private function addDeductionsSection($pdf, $records, $month, $year): void
    {
        $pdf->SectionTitle('Deductions');
        $pdf->TableHeader('Description', 'Amount', 'Balance');
        
        $deductionSums = [];
        $itemCodes = [];
        
        foreach ($records as $record) {
            if (!isset($deductionSums[$record->pname])) {
                $deductionSums[$record->pname] = 0;
                $itemCodes[$record->pname] = $record->itemcode;
            }
            $deductionSums[$record->pname] += $record->tamount;
        }
        
        foreach ($deductionSums as $pname => $totalAmount) {
            $balance = null;
            $itemcode = $itemCodes[$pname] ?? null;
            
            if ($itemcode) {
                $balance = Payhouse::where('itemcode', $itemcode)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->sum('balance');
            }
            
            $pdf->TableRow($pname, $totalAmount, $balance);
        }
        
        $total = array_sum($deductionSums);
        $pdf->TableTotal('Total Deductions', $total);
        $pdf->Ln(2);
    }

    /**
     * Add net pay section
     */
    private function addNetPaySection($pdf, $netPay, $totalNetPayAllModes): void
    {
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(200, 220, 255);
        $pdf->Cell(50, 7, 'Net Pay', 1, 0, 'L', true);
        $pdf->Cell(30, 7, number_format($netPay, 2), 1, 1, 'R', true);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(200, 220, 255);
        $pdf->Cell(50, 7, 'Net Take Home', 1, 0, 'L', true);
        $pdf->Cell(30, 7, number_format($totalNetPayAllModes, 2), 1, 1, 'R', true);

        $pdf->Ln(5);
    }

    /**
     * Add paymode summary section
     */
    private function addPaymodeSummarySection($pdf, $paymodeSummary): void
    {
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'NET PAY SUMMARY BY PAYMENT MODE', 0, 1, 'C');
        $pdf->Ln(5);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(80, 8, 'Payment Mode', 1, 0, 'C');
        $pdf->Cell(60, 8, 'Total Net Pay (KES)', 1, 0, 'C');
        $pdf->Cell(50, 8, 'No. of Employees', 1, 1, 'C');

        $pdf->SetFont('Arial', '', 10);
        foreach ($paymodeSummary['data'] as $summary) {
            $isBold = ($summary['paymode'] == 'GRAND TOTAL');
            $pdf->SetFont('Arial', $isBold ? 'B' : '', 10);
            
            $pdf->Cell(80, 8, $summary['paymode'], 1, 0, 'L');
            $pdf->Cell(60, 8, number_format($summary['total_net_pay'], 2), 1, 0, 'R');
            $pdf->Cell(50, 8, $summary['employee_count'], 1, 1, 'C');
        }
    }
}

if (!class_exists('OverallSummaryPDF')) {
    class OverallSummaryPDF extends \FPDF
    {
        private $schoolDetails;
         private $logoPath;
        private $heading;

        public function __construct($orientation, $unit, $size, $schoolDetails, $logoPath)
        {
            parent::__construct($orientation, $unit, $size);
            $this->schoolDetails = $schoolDetails;
            $this->logoPath = $logoPath;
        }

        public function setHeading($heading)
        {
            $this->heading = $heading;
        }
        function Header() {
            $this->SetFillColor(240, 240, 240); // Light grey background
            $this->Rect(0, 0, $this->GetPageWidth(), 30, 'F');
            $pageWidth = $this->GetPageWidth();
            
            $logoWidth = 25;
            $this->Image($this->logoPath, 8, 4, $logoWidth);
            
            $this->SetFont('Arial', 'B', 16);
            $this->SetTextColor(0, 51, 102); // Dark blue
            $this->SetXY($logoWidth + 20, 10);
            
            // FIX: Access object properties instead of array keys
            $this->Cell(0, 8, $this->schoolDetails->name ?? 'School Name Not Found', 0, 1);
            
            $this->SetFont('Arial', 'I', 10);
            $this->SetTextColor(100, 100, 100); // Dark grey
            $this->SetX($logoWidth + 20);
            $this->Cell(0, 5, $this->schoolDetails->motto ?? 'Motto Not Found', 0, 1);
            
            $this->SetFont('Arial', '', 8);
            $this->SetX($logoWidth + 20);
            $this->Cell(0, 5, "P.O. Box: " . ($this->schoolDetails->pobox ?? 'N/A') . " | Email: " . ($this->schoolDetails->email ?? 'N/A') . " | " . ($this->schoolDetails->physaddres ?? 'N/A'), 0, 1);
            
            $this->Ln(2);
            $this->Line(10, $this->GetY(), $pageWidth - 10, $this->GetY());
            $this->Ln(1);

            if ($this->heading) {
                $this->SetTextColor(0);
                $this->SetFont('Arial', 'B', 14);
                $this->Cell(0, 10, $this->heading, 0, 1, 'C');
                $this->Ln(1);
            }
        }

        // Footer
       function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->SetTextColor(100, 100, 100); // Dark grey
            $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
            $this->SetX(10);
            $this->Cell(0, 10, 'Generated on: ' . date('Y-m-d H:i:s'), 0, 0, 'L');
        }

        function SectionTitle($title) {
            $this->SetFont('Arial', 'B', 11);
            $this->SetFillColor(200, 220, 255);
            $this->Cell(140, 7, $title, 0, 1, 'L', true);
            $this->SetFont('Arial', '', 10);
        }

        function TableHeader($col1, $col2, $col3 = '') {
            $this->SetFont('Arial', 'B', 10);
            $this->SetFillColor(230, 230, 230);
            $this->Cell(50, 5, $col1, 1, 0, 'C', true);
            $this->Cell(30, 5, $col2, 1, 0, 'C', true);
            if ($col3 != '') {
                $this->Cell(30, 5, $col3, 1, 1, 'C', true);
            } else {
                $this->Ln();
            }
            $this->SetFont('Arial', '', 10);
        }

        function TableRow($description, $amount, $balance = null, $bold = false) {
            if ($bold) $this->SetFont('Arial', 'B', 10);
            $this->Cell(50, 5, $description, 'LR', 0, 'L');
            $this->Cell(30, 5, number_format($amount, 2), 'LR', 0, 'R');
            if ($balance !== null) {
                $this->Cell(30, 5, number_format($balance, 2), 'LR', 1, 'R');
            } else {
                $this->Ln(); // Move to the next line if no balance
            }
            if ($bold) $this->SetFont('Arial', '', 10);
        }

        function TableTotal($totalLabel, $totalAmount) {
            $this->SetFont('Arial', 'B', 9);
            $this->Cell(50, 7, $totalLabel, 1);
            $this->Cell(30, 7, number_format($totalAmount, 2), 1, 0, 'R');
            $this->Ln();
        }
    }
}