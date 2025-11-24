<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Ptype;
use App\Models\EmployeeDeduction;
use App\Models\Payhouse;
use App\Models\Employee;
use App\Models\Registration;
use App\Models\Structure;

class VarianceReportService
{
    protected $schoolDetails;
    protected $logoPath;

    public function __construct()
    {
        $this->schoolDetails = Structure::first();
        $this->setLogoPath();
    }

    /**
     * Generate variance report between two periods
     */
    public function generateVarianceReport(
        string $stmonth, 
        string $styear, 
        string $ndmonth, 
        string $ndyear, 
        string $pname, 
        ?string $staff3, 
        ?string $staff4
    ): string {
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

        // Fetch data for both periods
        $firstPeriodData = $this->fetchPeriodData($stmonth, $styear, $pname, $code, $staff3, $staff4, $allowedPayrollIds, $isLoanOrBalance);
        $secondPeriodData = $this->fetchPeriodData($ndmonth, $ndyear, $pname, $code, $staff3, $staff4, $allowedPayrollIds, $isLoanOrBalance);

        // Calculate variance data
        $varianceData = $this->calculateVarianceData($firstPeriodData, $secondPeriodData, $isLoanOrBalance);

        // Create PDF
        $pdf = new VariancePDF('L', 'mm', 'A4', $this->schoolDetails, "$stmonth $styear", "$ndmonth $ndyear", $pname, $code, $isLoanOrBalance, $this->logoPath);
        $pdf->AliasNbPages();
        $pdf->AddPage();

        // Table Header
        $header = $isLoanOrBalance ? 
            ['Work Number', 'Name', '1st Amount', '2nd Amount', 'Variance', '1st Balance', '2nd Balance', 'Bal. Variance'] : 
            ['Work Number', 'Name', '1st Amount', '2nd Amount', 'Variance'];

        // Create Table
        $pdf->CreateVarianceTable($header, $varianceData);

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
     * Fetch data for a specific period
     */
    private function fetchPeriodData(
        string $month, 
        string $year, 
        string $pname, 
        string $code, 
        ?string $staff3, 
        ?string $staff4, 
        array $allowedPayrollIds, 
        bool $isLoanOrBalance
    ): array {
        $query = Payhouse::from('payhouse as p')
            ->select(
                'p.WorkNo',
                DB::raw("CONCAT(e.FirstName, ' ', e.LastName) AS fullname"),
                'p.tamount'
            )
            ->join('tblemployees as e', 'p.WorkNo', '=', 'e.emp_id')
            ->join('ptypes as pt', 'p.pname', '=', 'pt.cname')
            ->join('registration as r', 'p.WorkNo', '=', 'r.empid')
            ->where('p.month', $month)
            ->where('p.year', $year)
            ->where('p.pname', $pname)
            ->where('e.Status', 'ACTIVE')
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

        // Convert to associative array keyed by WorkNo
        $data = [];
        foreach ($results as $row) {
            $data[$row->WorkNo] = [
                'WorkNo' => $row->WorkNo,
                'fullname' => $row->fullname,
                'tamount' => $row->tamount,
                'balance' => $isLoanOrBalance ? ($row->balance ?? 0) : 0
            ];
        }

        return $data;
    }
    private function setLogoPath(): void
    {
        $logoFile = $this->schoolDetails->logo ?? 'default.jpg';
        $this->logoPath = storage_path('app/public/students/' . $logoFile);
        
        if (!file_exists($this->logoPath)) {
            $this->logoPath = storage_path('app/public/students/default.png');
        }
    }

    /**
     * Calculate variance between two periods
     */
    private function calculateVarianceData(array $firstPeriodData, array $secondPeriodData, bool $isLoanOrBalance): array
    {
        $varianceData = [];
        $allWorkNumbers = array_unique(array_merge(array_keys($firstPeriodData), array_keys($secondPeriodData)));
        
        foreach ($allWorkNumbers as $workNo) {
            $firstAmount = $firstPeriodData[$workNo]['tamount'] ?? 0;
            $secondAmount = $secondPeriodData[$workNo]['tamount'] ?? 0;
            $variance = $secondAmount - $firstAmount;
            
            $firstBalance = 0;
            $secondBalance = 0;
            $balanceVariance = 0;
            
            if ($isLoanOrBalance) {
                $firstBalance = $firstPeriodData[$workNo]['balance'] ?? 0;
                $secondBalance = $secondPeriodData[$workNo]['balance'] ?? 0;
                $balanceVariance = $secondBalance - $firstBalance;
            }
            
            // Get employee name (prefer from first period, fallback to second)
            $fullname = $firstPeriodData[$workNo]['fullname'] ?? 
                       ($secondPeriodData[$workNo]['fullname'] ?? 'Unknown');
            
            $varianceData[] = [
                'WorkNo' => $workNo,
                'fullname' => $fullname,
                'first_amount' => $firstAmount,
                'second_amount' => $secondAmount,
                'variance' => $variance,
                'first_balance' => $firstBalance,
                'second_balance' => $secondBalance,
                'balance_variance' => $balanceVariance
            ];
        }

        // Sort by WorkNo
        usort($varianceData, function($a, $b) {
            return strcmp($a['WorkNo'], $b['WorkNo']);
        });

        return $varianceData;
    }
}

if (!class_exists('VariancePDF')) {
    class VariancePDF extends \FPDF
    {
        private $schoolDetails;
        private $stperiod;
        private $ndperiod;
        private $logoPath;
        private $pname;
        private $code;
        private $isLoanOrBalance;

        public function __construct($orientation, $unit, $size, $schoolDetails, $stperiod, $ndperiod, $pname, $code, $isLoanOrBalance, $logoPath)
        {
            parent::__construct($orientation, $unit, $size);
            $this->schoolDetails = $schoolDetails;
            $this->logoPath = $logoPath;
            $this->stperiod = $stperiod;
            $this->ndperiod = $ndperiod;
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
            $this->Cell(0, 10, $this->pname . ' Variance Report', 0, 1, 'C');
            
            // Period comparison
            $this->SetFont('Arial', 'I', 10);
            $this->Cell(0, 5, $this->stperiod . ' vs ' . $this->ndperiod, 0, 1, 'C');
            $this->Ln(2);
        }

        // Footer
        public function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
        }

        // Create variance table
        public function CreateVarianceTable($header, $data)
        {
            // Column widths - adjust based on whether it's loan/balance type
            if ($this->isLoanOrBalance) {
                $w = [25, 50, 25, 25, 25, 25, 25, 25]; // 8 columns
            } else {
                $w = [25, 70, 30, 30, 30]; // 5 columns
            }

            // Header
            $this->SetFont('Arial', 'B', 9);
            $this->SetFillColor(200, 200, 200);
            foreach ($header as $i => $col) {
                $this->Cell($w[$i], 7, $col, 1, 0, 'C', true);
            }
            $this->Ln();

            // Data
            $this->SetFont('Arial', '', 8);
            $this->SetFillColor(255, 255, 255);
            $fill = false;

            foreach ($data as $row) {
                // Work Number
                $this->Cell($w[0], 6, $row['WorkNo'], 'LR', 0, 'L', $fill);
                
                // Name
                $this->Cell($w[1], 6, $row['fullname'], 'LR', 0, 'L', $fill);
                
                // 1st Amount
                $this->Cell($w[2], 6, number_format($row['first_amount'], 2), 'LR', 0, 'R', $fill);
                
                // 2nd Amount
                $this->Cell($w[3], 6, number_format($row['second_amount'], 2), 'LR', 0, 'R', $fill);
                
                // Variance - color code based on value
                $variance = $row['variance'];
                if ($variance > 0) {
                    $this->SetTextColor(0, 128, 0); // Green for positive
                } elseif ($variance < 0) {
                    $this->SetTextColor(255, 0, 0); // Red for negative
                }
                $this->Cell($w[4], 6, number_format($variance, 2), 'LR', 0, 'R', $fill);
                $this->SetTextColor(0, 0, 0); // Reset color
                
                // Balance columns if applicable
                if ($this->isLoanOrBalance) {
                    // 1st Balance
                    $this->Cell($w[5], 6, number_format($row['first_balance'], 2), 'LR', 0, 'R', $fill);
                    
                    // 2nd Balance
                    $this->Cell($w[6], 6, number_format($row['second_balance'], 2), 'LR', 0, 'R', $fill);
                    
                    // Balance Variance
                    $balanceVariance = $row['balance_variance'];
                    if ($balanceVariance > 0) {
                        $this->SetTextColor(0, 128, 0); // Green for positive
                    } elseif ($balanceVariance < 0) {
                        $this->SetTextColor(255, 0, 0); // Red for negative
                    }
                    $this->Cell($w[7], 6, number_format($balanceVariance, 2), 'LR', 0, 'R', $fill);
                    $this->SetTextColor(0, 0, 0); // Reset color
                }
                
                $this->Ln();
                $fill = !$fill;
            }

            // Closing line
            $this->Cell(array_sum($w), 0, '', 'T');
        }
    }
}