<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Payhouse;
use App\Models\Registration;
use App\Models\Structure;

class PayrollVarianceService
{
    protected $schoolDetails;
    protected $logoPath;

    public function __construct()
    {
        $this->schoolDetails = Structure::first();
        $this->setLogoPath();
    }

    /**
     * Generate payroll summary variance report between two periods
     */
    public function generatePayrollVarianceReport(
        string $stmonth, 
        string $styear, 
        string $ndmonth, 
        string $ndyear
    ): string {
        // Include FPDF
        if (!class_exists('FPDF')) {
            require_once base_path('fpdf/fpdf.php');
        }

        $allowedPayrollIds = session('allowedPayroll', []);
        
        if (empty($allowedPayrollIds)) {
            throw new \Exception('No payroll access granted');
        }

        // Fetch data for both periods
        $firstPeriodData = $this->fetchPeriodData($stmonth, $styear, $allowedPayrollIds);
        $secondPeriodData = $this->fetchPeriodData($ndmonth, $ndyear, $allowedPayrollIds);

        // Calculate variance data
        $varianceData = $this->calculateVarianceData($firstPeriodData, $secondPeriodData);

        // Create PDF
        $pdf = new PayrollVariancePDF($this->schoolDetails, "$stmonth $styear", "$ndmonth $ndyear", $this->logoPath);
        $pdf->AliasNbPages();
        $pdf->AddPage();

        // Add data to PDF
        $this->addDataToPdf($pdf, $varianceData);

        return $pdf->Output('S');
    }

    /**
     * Fetch payroll data for a specific period
     */
    private function fetchPeriodData(string $month, string $year, array $allowedPayrollIds): array
    {
        $data = Payhouse::from('payhouse as p')
            ->select(
                'p.pname',
                'p.pcategory',
                'p.tamount',
                'p.WorkNo'
            )
            ->join('registration as r', 'p.WorkNo', '=', 'r.empid')
            ->where('p.month', $month)
            ->where('p.year', $year)
            ->whereIn('r.payrolty', $allowedPayrollIds)
            ->where(function($query) {
                $query->where('p.tamount', '!=', 0)
                      ->orWhere('p.balance', '!=', 0);
            })
            ->get();

        $processedData = [
            'earnings' => [],
            'deductions' => []
        ];

        foreach ($data as $row) {
            $category = '';
            switch ($row->pcategory) {
                case 'Payment':
                case 'Benefit':
                    $category = 'earnings';
                    break;
                case 'Deduction':
                    $category = 'deductions';
                    break;
                default:
                    continue 2; // Skip this row
            }
            
            if (!isset($processedData[$category][$row->pname])) {
                $processedData[$category][$row->pname] = [
                    'total' => 0,
                    'employees' => []
                ];
            }
            
            $processedData[$category][$row->pname]['total'] += $row->tamount;
            $processedData[$category][$row->pname]['employees'][] = $row->WorkNo;
        }

        // Remove duplicates and count unique employees
        foreach ($processedData as $category => $items) {
            foreach ($items as $pname => $data) {
                $processedData[$category][$pname]['employee_count'] = count(array_unique($data['employees']));
            }
        }

        return $processedData;
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
    private function calculateVarianceData(array $firstPeriodData, array $secondPeriodData): array
    {
        $varianceData = [
            'earnings' => [],
            'deductions' => []
        ];

        // Get all unique item names from both periods
        $allEarnings = array_unique(array_merge(
            array_keys($firstPeriodData['earnings']),
            array_keys($secondPeriodData['earnings'])
        ));

        $allDeductions = array_unique(array_merge(
            array_keys($firstPeriodData['deductions']),
            array_keys($secondPeriodData['deductions'])
        ));

        // Calculate variance for earnings
        foreach ($allEarnings as $pname) {
            $firstAmount = $firstPeriodData['earnings'][$pname]['total'] ?? 0;
            $secondAmount = $secondPeriodData['earnings'][$pname]['total'] ?? 0;
            $firstCount = $firstPeriodData['earnings'][$pname]['employee_count'] ?? 0;
            $secondCount = $secondPeriodData['earnings'][$pname]['employee_count'] ?? 0;
            
            $varianceData['earnings'][$pname] = [
                'first_amount' => $firstAmount,
                'second_amount' => $secondAmount,
                'variance' => $secondAmount - $firstAmount,
                'first_count' => $firstCount,
                'second_count' => $secondCount
            ];
        }

        // Calculate variance for deductions
        foreach ($allDeductions as $pname) {
            $firstAmount = $firstPeriodData['deductions'][$pname]['total'] ?? 0;
            $secondAmount = $secondPeriodData['deductions'][$pname]['total'] ?? 0;
            $firstCount = $firstPeriodData['deductions'][$pname]['employee_count'] ?? 0;
            $secondCount = $secondPeriodData['deductions'][$pname]['employee_count'] ?? 0;
            
            $varianceData['deductions'][$pname] = [
                'first_amount' => $firstAmount,
                'second_amount' => $secondAmount,
                'variance' => $secondAmount - $firstAmount,
                'first_count' => $firstCount,
                'second_count' => $secondCount
            ];
        }

        return $varianceData;
    }

    /**
     * Add data to PDF
     */
    private function addDataToPdf($pdf, array $varianceData): void
    {
        // Earnings Section
        $pdf->SectionTitle('Earnings');
        $pdf->TableHeader();
        
        $totalFirstEarnings = 0;
        $totalSecondEarnings = 0;
        
        foreach ($varianceData['earnings'] as $pname => $data) {
            $pdf->TableRow(
                $pname, 
                $data['first_amount'], 
                $data['first_count'],
                $data['second_amount'], 
                $data['second_count'],
                $data['variance']
            );
            
            $totalFirstEarnings += $data['first_amount'];
            $totalSecondEarnings += $data['second_amount'];
        }
        
        $earningsVariance = $totalSecondEarnings - $totalFirstEarnings;
        $pdf->TableTotal('Total Earnings', $totalFirstEarnings, $totalSecondEarnings, $earningsVariance);
        $pdf->Ln(2);

        // Deductions Section
        $pdf->SectionTitle('Deductions');
        $pdf->TableHeader();
        
        $totalFirstDeductions = 0;
        $totalSecondDeductions = 0;
        
        foreach ($varianceData['deductions'] as $pname => $data) {
            $pdf->TableRow(
                $pname, 
                $data['first_amount'], 
                $data['first_count'],
                $data['second_amount'], 
                $data['second_count'],
                $data['variance']
            );
            
            $totalFirstDeductions += $data['first_amount'];
            $totalSecondDeductions += $data['second_amount'];
        }
        
        $deductionsVariance = $totalSecondDeductions - $totalFirstDeductions;
        $pdf->TableTotal('Total Deductions', $totalFirstDeductions, $totalSecondDeductions, $deductionsVariance);
        $pdf->Ln(1);

        // Net Pay Summary
        $firstNetPay = $totalFirstEarnings - $totalFirstDeductions;
        $secondNetPay = $totalSecondEarnings - $totalSecondDeductions;
        $netPayVariance = $secondNetPay - $firstNetPay;
        
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(200, 220, 255);
        $pdf->Cell(50, 7, 'Net Pay', 1, 0, 'L', true);
        $pdf->Cell(25, 7, number_format($firstNetPay, 2), 1, 0, 'R', true);
        $pdf->Cell(20, 7, '', 1, 0, 'C', true);
        $pdf->Cell(25, 7, number_format($secondNetPay, 2), 1, 0, 'R', true);
        $pdf->Cell(20, 7, '', 1, 0, 'C', true);
        
        if ($netPayVariance > 0) {
            $pdf->SetTextColor(0, 128, 0);
        } elseif ($netPayVariance < 0) {
            $pdf->SetTextColor(255, 0, 0);
        }
        
        $netPayPercentage = $firstNetPay != 0 ? (($netPayVariance / $firstNetPay) * 100) : 0;
        $pdf->Cell(25, 7, number_format($netPayVariance, 2), 1, 0, 'R', true);
        $pdf->Cell(15, 7, number_format($netPayPercentage, 1) . '%', 1, 1, 'R', true);

        $pdf->AddSignatures();
    }
}

if (!class_exists('PayrollVariancePDF')) {
    class PayrollVariancePDF extends \FPDF
    {
        private $schoolDetails;
        private $stperiod;
        private $ndperiod;
        private $logoPath;

        public function __construct($schoolDetails, $stperiod, $ndperiod, $logoPath)
        {
            parent::__construct();
            $this->schoolDetails = $schoolDetails;
             $this->logoPath = $logoPath;
            $this->stperiod = $stperiod;
            $this->ndperiod = $ndperiod;
        }

        // Header
        

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

            $this->SetFont('Arial', 'B', 14);
            $this->Cell(0, 10, 'Payroll Variance Report', 0, 1, 'C');
            
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

         function SectionTitle($title) {
            $this->SetFont('Arial', 'B', 12);
            $this->SetFillColor(200, 220, 255);
            $this->Cell(0, 8, $title, 0, 1, 'L', true);
            $this->Ln(2);
        }

        function TableHeader() {
            $this->SetFont('Arial', 'B', 10);
            $this->SetFillColor(230, 230, 230);
            $this->Cell(50, 7, 'Description', 1, 0, 'C', true);
            $this->Cell(25, 7, '1st Period', 1, 0, 'C', true);
            $this->Cell(20, 7, 'Emp Count', 1, 0, 'C', true);
            $this->Cell(25, 7, '2nd Period', 1, 0, 'C', true);
            $this->Cell(20, 7, 'Emp Count', 1, 0, 'C', true);
            $this->Cell(25, 7, 'Variance', 1, 0, 'C', true);
            $this->Cell(15, 7, '%', 1, 1, 'C', true);
        }
         function AddSignatures() {
            $this->SetFont('Arial', 'B', 9);
            $this->Ln(2); // Add some space before the signatures
    
            $this->SetFont('Arial', 'B', 9);
            $this->Cell(60, 10, 'Prepared By', 1, 0, 'L');
            $this->Cell(60, 10, 'Date', 1, 1, 'L'); // Move to the next line
    
            $this->Cell(60, 10, 'Checked By', 1, 0, 'L');
            $this->Cell(60, 10, 'Date', 1, 1, 'L'); // Move to the next line
    
            $this->Cell(60, 10, 'Authorised By', 1, 0, 'L');
            $this->Cell(60, 10, 'Date', 1, 1, 'L'); // Move to the next line
        }

        function TableRow($description, $firstAmount, $firstCount, $secondAmount, $secondCount, $variance) {
            $this->SetFont('Arial', '', 9);
            
            // Calculate percentage change
            $percentage = 0;
            if ($firstAmount != 0) {
                $percentage = (($secondAmount - $firstAmount) / $firstAmount) * 100;
            } elseif ($secondAmount != 0) {
                $percentage = 100; // If first is 0 and second is not, it's 100% increase
            }
            
            $this->Cell(50, 6, $description, 1, 0, 'L');
            $this->Cell(25, 6, number_format($firstAmount, 2), 1, 0, 'R');
            $this->Cell(20, 6, $firstCount, 1, 0, 'C');
            $this->Cell(25, 6, number_format($secondAmount, 2), 1, 0, 'R');
            $this->Cell(20, 6, $secondCount, 1, 0, 'C');
            
            // Color code variance
            if ($variance > 0) {
                $this->SetTextColor(0, 128, 0); // Green
            } elseif ($variance < 0) {
                $this->SetTextColor(255, 0, 0); // Red
            } else {
                $this->SetTextColor(0, 0, 0); // Black
            }
            
            $this->Cell(25, 6, number_format($variance, 2), 1, 0, 'R');
            $this->Cell(15, 6, number_format($percentage, 1) . '%', 1, 1, 'R');
            
            $this->SetTextColor(0, 0, 0); // Reset color
        }

        function TableTotal($title, $firstTotal, $secondTotal, $variance) {
            $this->SetFont('Arial', 'B', 10);
            $this->SetFillColor(220, 220, 220);
            
            $percentage = 0;
            if ($firstTotal != 0) {
                $percentage = (($secondTotal - $firstTotal) / $firstTotal) * 100;
            } elseif ($secondTotal != 0) {
                $percentage = 100;
            }
            
            $this->Cell(50, 7, $title, 1, 0, 'L', true);
            $this->Cell(25, 7, number_format($firstTotal, 2), 1, 0, 'R', true);
            $this->Cell(20, 7, '', 1, 0, 'C', true);
            $this->Cell(25, 7, number_format($secondTotal, 2), 1, 0, 'R', true);
            $this->Cell(20, 7, '', 1, 0, 'C', true);
            
            // Color code total variance
            if ($variance > 0) {
                $this->SetTextColor(0, 128, 0);
            } elseif ($variance < 0) {
                $this->SetTextColor(255, 0, 0);
            }
            
            $this->Cell(25, 7, number_format($variance, 2), 1, 0, 'R', true);
            $this->Cell(15, 7, number_format($percentage, 1) . '%', 1, 1, 'R', true);
            
            $this->SetTextColor(0, 0, 0);
        }
    }
}