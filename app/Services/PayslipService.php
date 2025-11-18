<?php

namespace App\Services;

use App\Models\Agents;
use Illuminate\Support\Facades\DB;
use App\Models\Payhouse;
use App\Models\Structure;
use App\Models\Payincludes;
use App\Models\Registration;
use App\Models\Pmessage;
use Illuminate\Support\Facades\Log;

class PayslipService
{
    protected $schoolDetails;
    protected $logoPath;

    public function __construct()
    {
        $this->schoolDetails = Structure::first();
        $this->setLogoPath();
    }

    /**
     * Generate payslip PDF
     */
    public function generatePayslip(string $staffid, string $month, string $year): string
    {
        // Include FPDF
        if (!class_exists('FPDF')) {
            require_once base_path('fpdf/fpdf.php');
        }

        // Fetch employee data
       $employee = Agents::where('emp_id', $staffid)
    ->select(DB::raw("CONCAT(FirstName, ' ', LastName) AS fullname"))
    ->first()
    ->toArray();

if (!$employee) {
    throw new \Exception('Employee not found');
}


        // Fetch payslip data
        $payslipData = Payhouse::where('WorkNo', $staffid)
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        // Categorize records
        $categorizedRecords = $this->categorizeRecords($payslipData);

        // Calculate totals
        $totals = $this->calculateTotals($categorizedRecords);

        // Create PDF
        $pdf = new PayslipPDF('P', 'mm', 'A4', $this->schoolDetails, $this->logoPath);
        $pdf->AliasNbPages();
         $pdf->setHeading("Payslip Report");
        $pdf->AddPage();
        $pdf->SetFont('Times', '', 12);

        // Add employee header
        $this->addEmployeeHeader($pdf, $employee, $staffid, $month, $year);

        // Add sections
        $this->addGrossSalarySection($pdf, $categorizedRecords['grossSalary'], $staffid, $month, $year);
        $this->addReliefOnTaxableSection($pdf, $categorizedRecords['reliefOnTaxable']);
        $this->addTaxableIncomeSection($pdf, $totals['taxableIncome']);
        $this->addTaxAndPayeSection($pdf, $categorizedRecords['tax'], $categorizedRecords['reliefOnPaye'], $totals['paye']);
        $this->addDeductionsSection($pdf, $categorizedRecords['deductions'], $staffid, $month, $year);
        $this->addNetPaySection($pdf, $totals['netPay']);
        $this->addEmployeeDetailsSection($pdf, $staffid);

        // Add message if exists
        $this->addPdfMessage($pdf, $month, $year);

        return $pdf->Output('S');
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
                    if ($row->pname != 'Total Gross Salary') {
                        $categories['grossSalary'][] = $row;
                    }
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
     * Set logo path
     */
    private function setLogoPath(): void
    {
        $logoFile = $this->schoolDetails->logo ?? 'default.jpg';
        $this->logoPath = storage_path('app/public/students/' . $logoFile);
        
        if (!file_exists($this->logoPath)) {
            $this->logoPath = storage_path('app/public/students/default.png');
        }
    }

    /**
     * Add employee header to PDF
     */
   private function addEmployeeHeader($pdf, $employee, $staffid, $month, $year): void
{
    $pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(90, 10, 'Agent: ' . $employee['fullname'], 0, 0, 'L');
$pdf->Cell(50, 10, 'Agent Number: ' . $staffid, 0, 0, 'L');
$pdf->Cell(0, 10, 'Period: ' . $month . ' ' . $year, 0, 1, 'L');
$pdf->Ln(0);

}

    /**
     * Add gross salary section
     */
    private function addGrossSalarySection($pdf, $records, $staffid, $month, $year): void
    {
        $pdf->SectionTitle('Gross Salary');
        $pdf->TableHeader('Description', 'Amount', 'Bal/Qnt');
        
        foreach ($records as $record) {
            $quantity = $this->getQuantityForRecord($record, $staffid, $month, $year);
            $pdf->TableRow($record->pname, $record->tamount, $quantity);
        }
        
        $total = collect($records)->sum('tamount');
        $pdf->TableTotal('Total Gross Salary', $total);
        $pdf->Ln(0);
    }

    /**
     * Get quantity for overtime records
     */
    private function getQuantityForRecord($record, $staffid, $month, $year): ?string
    {
        if (!$record->itemcode) {
            return null;
        }

        $monthNumber = date('n', strtotime("1 $month"));

        $quantity = DB::table('otrecords')
            ->where('Pcode', $record->itemcode)
            ->where('WorkNo', $staffid)
            ->whereYear('odate', $year)
            ->whereMonth('odate', $monthNumber)
            ->sum('quantity');

        return $quantity ?: null;
    }

    /**
     * Add relief on taxable section
     */
    private function addReliefOnTaxableSection($pdf, $records): void
    {
        $pdf->SectionTitle('Relief on Taxable');
        $pdf->TableHeader('Description', 'Amount');
        
        foreach ($records as $record) {
            $pdf->TableRow($record->pname, $record->tamount);
        }
        
        $total = collect($records)->sum('tamount');
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
        
        foreach ($taxRecords as $record) {
            $pdf->TableRow($record->pname, $record->tamount);
        }
        
        foreach ($reliefRecords as $record) {
            $pdf->TableRow('  ' . $record->pname, $record->tamount);
        }
        
        $reliefTotal = collect($reliefRecords)->sum('tamount');
        $pdf->TableRow('Relief on PAYE', $reliefTotal);
        $pdf->TableTotal('PAYE', $paye);
        $pdf->Ln(0);
    }

    /**
     * Add deductions section
     */
    private function addDeductionsSection($pdf, $records, $staffid, $month, $year): void
    {
        $pdf->SectionTitle('Deductions');
        $pdf->TableHeader('Description', 'Amount');
        
        foreach ($records as $record) {
            $balance = $this->getBalanceForDeduction($record, $staffid, $month, $year);
            $pdf->TableRow($record->pname, $record->tamount, $balance);
        }
        
        $total = collect($records)->sum('tamount');
        $pdf->TableTotal('Total Deductions', $total);
        $pdf->Ln(0);
    }

    /**
     * Get balance for deduction records
     */
    private function getBalanceForDeduction($record, $staffid, $month, $year): ?string
    {
        if (!$record->itemcode) {
            return null;
        }

        $balance = DB::table('payhouse')
            ->where('itemcode', $record->itemcode)
            ->where('WorkNo', $staffid)
            ->where('month', $month)
            ->where('year', $year)
            ->value('balance');

        return $balance;
    }

    /**
     * Add net pay section
     */
    private function addNetPaySection($pdf, $netPay): void
    {
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(200, 220, 255);
        $pdf->Cell(50, 7, 'Net Pay', 1, 0, 'L', true);
        $pdf->Cell(30, 7, number_format($netPay, 2), 1, 1, 'R', true);
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetFillColor(230, 230, 230);
    }

    /**
     * Add employee details section
     */
    private function addEmployeeDetailsSection($pdf, $staffid): void
    {
        $payIncludes = Payincludes::first();
        $employeeDetails = Registration::where('empid', $staffid)->first();

        if (!$payIncludes || !$employeeDetails) {
            return;
        }

        $fields = [
            'nhifno' => ['label' => 'NHIF:', 'column' => 'nhif'],
            'krano'  => ['label' => 'KRA PIN:', 'column' => 'kra'],
            'penno'  => ['label' => 'Pension:', 'column' => 'pension'],
            'nssfno' => ['label' => 'NSSF:', 'column' => 'nssf'],
            'bankacc' => ['label' => 'Bank Acc:', 'column' => 'AccountNo']
        ];

        foreach ($fields as $field => $data) {
            if ($payIncludes->$field === 'YES') {
                $value = $employeeDetails->{$data['column']} ?? '';
                $pdf->Cell(50, 5, $data['label'], 1, 0, 'L', true);
                $pdf->Cell(30, 5, $value, 1, 1, 'R', true);
            }
        }
    }

    /**
     * Add PDF message if exists
     */
    private function addPdfMessage($pdf, $month, $year): void
    {
        $message = Pmessage::where('mmonth', $month)
            ->where('yyear', $year)
            ->value('message');

        if (!$message) {
            return;
        }

        $this->parsePdfMessage($pdf, $message);
    }

    /**
     * Parse and add formatted message to PDF
     */
    private function parsePdfMessage($pdf, string $message): void
    {
        $message = trim(html_entity_decode($message));
        
        if (empty($message)) {
            return;
        }

        $pdf->Ln(1);
        $pdf->SetFont('Arial', '', 10);

        $tagStyles = [
            'b' => 'B',
            'strong' => 'B',
            'i' => 'I',
            'em' => 'I',
            'u' => 'U'
        ];

        $lineBreakTags = ['br', 'p'];

        $pattern = '/<\/?([a-z]+)[^>]*>|([^<]+)/i';
        preg_match_all($pattern, $message, $matches, PREG_SET_ORDER);
        
        $currentStyle = '';
        
        foreach ($matches as $match) {
            if (!empty($match[1])) {
                $tag = strtolower($match[1]);
                $isClosingTag = strpos($match[0], '</') === 0;
                
                if (isset($tagStyles[$tag])) {
                    $style = $tagStyles[$tag];
                    if ($isClosingTag) {
                        $currentStyle = str_replace($style, '', $currentStyle);
                    } else {
                        $currentStyle .= $style;
                    }
                }
                
                if (in_array($tag, $lineBreakTags) && $isClosingTag) {
                    $pdf->Ln();
                }
            } elseif (!empty($match[2])) {
                $pdf->SetFont('Arial', $currentStyle, 10);
                $pdf->Write(5, $match[2]);
            }
        }
    }
}if (!class_exists('PayslipPDF')) {
    class PayslipPDF extends \FPDF
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
            $this->Cell(0, 7, $title, 0, 1, 'L', true);
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
        
    
        function TableTotal($description, $amount) {
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(50, 5, $description, 'LTB', 0, 'L');
            $this->Cell(30, 5, number_format($amount, 2), 'TBR', 1, 'R');
            $this->SetFont('Arial', '', 10);
        }
    }
}