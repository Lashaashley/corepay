<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Ptype;
use App\Models\EmployeeDeduction;
use App\Models\Payhouse;
use App\Models\Employee;
use App\Models\Registration;
use App\Models\Structure;

class PayrollItemsService
{
    protected $schoolDetails;
    protected $logoPath;
    
    public function __construct()
    {
        $this->schoolDetails = Structure::first();
        $this->setLogoPath();
    }

    /**
     * Generate payroll items listing report
     */
    public function generatePayrollItemsReport(string $month, string $year, string $pname, ?string $staff3, ?string $staff4): string
    {
        // Include FPDF
        if (!class_exists('FPDF')) {
            require_once base_path('fpdf/fpdf.php');
        }

        $allowedPayrollIds = session('allowedPayroll', []);
        
        if (empty($allowedPayrollIds)) {
            throw new \Exception('No payroll access granted');
        }

        // Get code from ptypes
        $ptype = Ptype::where('cname', $pname)->first();
        if (!$ptype) {
            throw new \Exception('Payroll item not found');
        }
        $code = $ptype->code;

        // Check if it's a loan or balance type
        $isLoanOrBalance = $this->isLoanOrBalanceType($code);

        // Fetch report data
        $reportData = $this->fetchReportData($month, $year, $pname, $code, $staff3, $staff4, $allowedPayrollIds, $isLoanOrBalance);

        // Create PDF
        $pdf = new PayrollItemsPDF('P', 'mm', 'A4', $this->schoolDetails, "$month $year", $pname, $code, $isLoanOrBalance, $this->logoPath);
        $pdf->AliasNbPages();
        $pdf->AddPage();

        // Table Header
        $header = $isLoanOrBalance 
            ? ['Work Number', 'Name', 'Amount', 'Balance'] 
            : ['Work Number', 'Name', 'Amount'];

        // Create Table
        $pdf->CreateTable($header, $reportData);

        return $pdf->Output('S');
    }


    public function generateEarningsReport(string $month, string $year, string $pcate, ?string $staff3, ?string $staff4): string
    {
        // Include FPDF
        if (!class_exists('FPDF')) {
            require_once base_path('fpdf/fpdf.php');
        }

        $allowedPayrollIds = session('allowedPayroll', []);
        
        if (empty($allowedPayrollIds)) {
            throw new \Exception('No payroll access granted');
        }

        // Get code from ptypes
        $ptype = Ptype::where('cname', $pcate)->first();
        if (!$ptype) {
            throw new \Exception('Payroll item not found');
        }
        $code = $ptype->code;

        // Check if it's a loan or balance type
        $isLoanOrBalance = $this->isLoanOrBalanceType($code);

        // Fetch report data
        $reportData = $this->FetchEarnings($month, $year, $pcate, $code, $staff3, $staff4, $allowedPayrollIds, $isLoanOrBalance);

        // Create PDF
        $pdf = new PayrollItemsPDF('P', 'mm', 'A4', $this->schoolDetails, "$month $year", $pcate, $code, $isLoanOrBalance, $this->logoPath);
        $pdf->AliasNbPages();
        $pdf->AddPage();

        // Table Header
        $header = $isLoanOrBalance 
            ? ['Agent No', 'Name', 'Amount', 'Balance'] 
            : ['Agent No', 'Name', 'Amount'];

        // Create Table
        $pdf->CreateTable($header, $reportData);

        return $pdf->Output('S');
    }

    public function generateNetpay(string $month, string $year, string $pcate, ?string $staff3, ?string $staff4): string
    {
        // Include FPDF
        if (!class_exists('FPDF')) {
            require_once base_path('fpdf/fpdf.php');
        }

        $allowedPayrollIds = session('allowedPayroll', []);
        
        if (empty($allowedPayrollIds)) {
            throw new \Exception('No payroll access granted');
        }

        // Get code from ptypes
        $ptype = Ptype::where('cname', $pcate)->first();
        if (!$ptype) {
            throw new \Exception('Payroll item not found');
        }
        $code = $ptype->code;

        // Check if it's a loan or balance type
        $isLoanOrBalance = $this->isLoanOrBalanceType($code);

        // Fetch report data
        $reportData = $this->fetchNetPay($month, $year, $pcate, $code, $staff3, $staff4, $allowedPayrollIds, $isLoanOrBalance);

        // Create PDF
        $pdf = new PayrollItemsPDF('P', 'mm', 'A4', $this->schoolDetails, "$month $year", $pcate, $code, $isLoanOrBalance, $this->logoPath);
        $pdf->AliasNbPages();
        $pdf->AddPage();

        // Table Header
        $header = $isLoanOrBalance 
            ? ['Agent No', 'Name', 'Amount', 'Balance'] 
            : ['Agent No', 'Name', 'Amount'];

        // Create Table
        $pdf->CreateTable($header, $reportData);

        return $pdf->Output('S');
    }

    /**
     * Check if the payroll item is a loan or balance type
     */
    private function isLoanOrBalanceType(string $code): bool
    {
        $deduction = EmployeeDeduction::where('PCode', $code)
            ->select('loanshares')
            ->first();

        return $deduction && in_array($deduction->loanshares, ['balance', 'loan']);
    }

    /**
     * Fetch report data from database
     */
    private function fetchReportData(string $month, string $year, string $pname, string $code, ?string $staff3, ?string $staff4, array $allowedPayrollIds, bool $isLoanOrBalance): array
    {
        $query = Payhouse::from('payhouse as p')
            ->select(
                'p.WorkNo',
                DB::raw("CONCAT(e.FirstName, ' ', e.LastName) AS fullname"),
                'p.tamount',
                'pt.code'
            )
            ->join('tblemployees as e', 'p.WorkNo', '=', 'e.emp_id')
            ->join('ptypes as pt', 'p.pname', '=', 'pt.cname')
            ->join('registration as r', 'p.WorkNo', '=', 'r.empid')
            ->where('p.month', $month)
            ->where('p.year', $year)
            ->where('p.pname', $pname)
            ->whereIn('r.payrolty', $allowedPayrollIds);

        // Add staff range filter if provided
        if ($staff3 && $staff4) {
            $query->whereBetween('p.WorkNo', [$staff3, $staff4]);
        } elseif ($staff3) {
            $query->where('p.WorkNo', '>=', $staff3);
        } elseif ($staff4) {
            $query->where('p.WorkNo', '<=', $staff4);
        }

        // Add balance join and select if it's a loan/balance type
        if ($isLoanOrBalance) {
            $query->leftJoin('payhouse as ph', function($join) use ($code, $month, $year) {
                $join->on('p.WorkNo', '=', 'ph.WorkNo')
                    ->where('ph.itemcode', $code)
                    ->where('ph.month', $month)
                    ->where('ph.year', $year);
            })
            ->addSelect('ph.balance');
        }

        $query->orderBy('p.WorkNo');

        $results = $query->get();

        // Convert to array format for PDF
        return $results->map(function($item) use ($isLoanOrBalance) {
            $row = [
                'WorkNo' => $item->WorkNo,
                'fullname' => $item->fullname,
                'tamount' => $item->tamount
            ];

            if ($isLoanOrBalance) {
                $row['balance'] = $item->balance;
            }

            return $row;
        })->toArray();
    }

    private function fetchNetPay(string $month, string $year, string $pname, string $code, ?string $staff3, ?string $staff4, array $allowedPayrollIds, bool $isLoanOrBalance): array
    {
        $query = Payhouse::from('payhouse as p')
            ->select(
                'p.WorkNo',
                DB::raw("CONCAT(e.FirstName, ' ', e.LastName) AS fullname"),
                'p.tamount',
                'pt.code'
            )
            ->join('tblemployees as e', 'p.WorkNo', '=', 'e.emp_id')
            ->join('ptypes as pt', 'p.pname', '=', 'pt.cname')
            ->join('registration as r', 'p.WorkNo', '=', 'r.empid')
            ->where('p.month', $month)
            ->where('p.year', $year)
            ->where('p.pname', $pname)
            ->whereIn('r.payrolty', $allowedPayrollIds);

        // Add staff range filter if provided
        if ($staff3 && $staff4) {
            $query->whereBetween('p.WorkNo', [$staff3, $staff4]);
        } elseif ($staff3) {
            $query->where('p.WorkNo', '>=', $staff3);
        } elseif ($staff4) {
            $query->where('p.WorkNo', '<=', $staff4);
        }

        // Add balance join and select if it's a loan/balance type
        if ($isLoanOrBalance) {
            $query->leftJoin('payhouse as ph', function($join) use ($code, $month, $year) {
                $join->on('p.WorkNo', '=', 'ph.WorkNo')
                    ->where('ph.itemcode', $code)
                    ->where('ph.month', $month)
                    ->where('ph.year', $year);
            })
            ->addSelect('ph.balance');
        }

        $query->orderBy('p.WorkNo');

        $results = $query->get();

        // Convert to array format for PDF
        return $results->map(function($item) use ($isLoanOrBalance) {
            $row = [
                'WorkNo' => $item->WorkNo,
                'fullname' => $item->fullname,
                'tamount' => $item->tamount
            ];

            if ($isLoanOrBalance) {
                $row['balance'] = $item->balance;
            }

            return $row;
        })->toArray();
    }



    private function FetchEarnings(string $month, string $year, string $pcate, string $code, ?string $staff3, ?string $staff4, array $allowedPayrollIds, bool $isLoanOrBalance): array
    {
        $query = EmployeeDeduction::from('employeedeductions as p')
            ->select(
                'p.WorkNo',
                DB::raw("CONCAT(e.FirstName, ' ', e.LastName) AS fullname"),
                'p.Amount',
                'pt.code'
            )
            ->join('tblemployees as e', 'p.WorkNo', '=', 'e.emp_id')
            ->join('ptypes as pt', 'p.pcate', '=', 'pt.cname')
            ->join('registration as r', 'p.WorkNo', '=', 'r.empid')
            ->where('p.month', $month)
            ->where('p.year', $year)
            ->where('p.pcate', $pcate)
            ->whereIn('r.payrolty', $allowedPayrollIds);

        // Add staff range filter if provided
        if ($staff3 && $staff4) {
            $query->whereBetween('p.WorkNo', [$staff3, $staff4]);
        } elseif ($staff3) {
            $query->where('p.WorkNo', '>=', $staff3);
        } elseif ($staff4) {
            $query->where('p.WorkNo', '<=', $staff4);
        }

        // Add balance join and select if it's a loan/balance type
        if ($isLoanOrBalance) {
            $query->leftJoin('payhouse as ph', function($join) use ($code, $month, $year) {
                $join->on('p.WorkNo', '=', 'ph.WorkNo')
                    ->where('ph.itemcode', $code)
                    ->where('ph.month', $month)
                    ->where('ph.year', $year);
            })
            ->addSelect('ph.balance');
        }

        $query->orderBy('p.WorkNo');

        $results = $query->get();

        // Convert to array format for PDF
        return $results->map(function($item) use ($isLoanOrBalance) {
            $row = [
                'WorkNo' => $item->WorkNo,
                'fullname' => $item->fullname,
                'tamount' => $item->Amount
            ];

            if ($isLoanOrBalance) {
                $row['balance'] = $item->balance;
            }

            return $row;
        })->toArray();
    }

    private function setLogoPath(): void
    {
        $logoFile = $this->schoolDetails->logo ?? 'default.jpg';
        $this->logoPath = storage_path('app/public/students/' . $logoFile);
        
        if (!file_exists($this->logoPath)) {
            $this->logoPath = storage_path('app/public/students/default.png');
        }
    }
}


if (!class_exists('PayrollItemsPDF')) {
    class PayrollItemsPDF extends \FPDF
    {
        private $schoolDetails;
        private $period;
        private $pname;
        private $code;
        private $isLoanOrBalance;
        private $logoPath;

        public function __construct($orientation, $unit, $size, $schoolDetails, $period, $pname, $code, $isLoanOrBalance, $logoPath)
        {
            parent::__construct($orientation, $unit, $size);
            $this->schoolDetails = $schoolDetails;
            $this->logoPath = $logoPath;
            $this->period = $period;
            $this->pname = $pname;
            $this->code = $code;
            $this->isLoanOrBalance = $isLoanOrBalance;
        }

        // Header
        public function Header()
        {
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
            
            // Report title
            $this->SetFont('Arial', 'B', 14);
            $this->Cell(0, 10, $this->pname . ' Listing - ' . $this->period, 0, 1, 'C');
            $this->Ln(2);
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

        // Create table
        public function CreateTable($header, $data)
{
    // Column widths
    if ($this->isLoanOrBalance) {
        $w = [40, 80, 35, 35]; // WorkNo, Name, Amount, Balance
    } else {
        $w = [40, 100, 50]; // WorkNo, Name, Amount
    }

    // Header
    $this->SetFont('Arial', 'B', 10);
    $this->SetFillColor(200, 200, 200);

    for ($i = 0; $i < count($header); $i++) {
        $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
    }
    $this->Ln();

    // Data
    $this->SetFont('Arial', '', 9);
    $this->SetFillColor(255, 255, 255);
    $fill = false;

    // Totals
    $totalAmount = 0;
    $totalBalance = 0;

    foreach ($data as $row) {

        $amount = isset($row['tamount']) ? (float)$row['tamount'] : 0;
        $balanceVal = isset($row['balance']) ? (float)$row['balance'] : 0;

        $totalAmount += $amount;
        $totalBalance += $balanceVal;

        // Work Number
        $this->Cell($w[0], 6, $row['WorkNo'], 'LR', 0, 'L', $fill);

        // Name
        $this->Cell($w[1], 6, $row['fullname'], 'LR', 0, 'L', $fill);

        // Amount
        $this->Cell($w[2], 6, number_format($amount, 2), 'LR', 0, 'R', $fill);

        // Balance (if applicable)
        if ($this->isLoanOrBalance) {
            $this->Cell($w[3], 6, number_format($balanceVal, 2), 'LR', 0, 'R', $fill);
        }

        $this->Ln();
        $fill = !$fill;
    }

    // TOTAL ROW
    $this->SetFont('Arial', 'B', 10);
    $this->SetFillColor(220, 220, 220);

    // Total label spanning first 2 columns
    $this->Cell($w[0] + $w[1], 7, 'TOTAL', 1, 0, 'R', true);

    // Total amount
    $this->Cell($w[2], 7, number_format($totalAmount, 2), 1, 0, 'R', true);

    // Total balance if applicable
    if ($this->isLoanOrBalance) {
        $this->Cell($w[3], 7, number_format($totalBalance, 2), 1, 0, 'R', true);
    }

    $this->Ln();

    // Closing line
    $this->Cell(array_sum($w), 0, '', 'T');
}

    }
}