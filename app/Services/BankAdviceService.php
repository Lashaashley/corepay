<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Payhouse;
use App\Models\Agents;
use App\Models\Registration;
use App\Models\Structure;

class BankAdviceService
{
    protected $schoolDetails;
    protected $logoPath;

    public function __construct()
    {
        $this->schoolDetails = Structure::first();
        $this->setLogoPath();
    }

    /**
     * Generate bank advice report
     */
    public function generateBankAdvice(string $month, string $year, string $recintres): string
    {
        // Include FPDF
        if (!class_exists('FPDF')) {
            require_once base_path('fpdf/fpdf.php');
        }

        $allowedPayrollIds = session('allowedPayroll', []);
        
        if (empty($allowedPayrollIds)) {
            throw new \Exception('No payroll access granted');
        }

        // Fetch bank advice data
        $bankData = $this->fetchBankAdviceData($month, $year, $recintres, $allowedPayrollIds);

        // Create PDF
        $pdf = new BankAdvicePDF('P', 'mm', 'A4', $this->schoolDetails, $this->logoPath, "$month $year");
        $pdf->AliasNbPages();
        $pdf->AddPage();

        // Add data to PDF
        $this->addDataToPdf($pdf, $bankData);

        return $pdf->Output('S');
    }

    /**
     * Fetch bank advice data grouped by bank and branch
     */
    private function fetchBankAdviceData(string $month, string $year, string $recintres, array $allowedPayrollIds): array
    {
        $data = Payhouse::from('payhouse as p')
            ->select(
                'p.WorkNo',
                DB::raw("CONCAT(e.FirstName, ' ', e.LastName) AS Name"),
                'r.Bank',
                'r.Branch',
                'r.AccountNo',
                'p.tamount AS NET_PAY'
            )
            ->join('tblemployees as e', 'p.WorkNo', '=', 'e.emp_id')
            ->join('registration as r', 'p.WorkNo', '=', 'r.empid')
            ->where('p.month', $month)
            ->where('p.year', $year)
            ->where('p.pname', 'NET PAY')
            ->where('p.tamount', '>', 1)
            ->where('r.paymode', $recintres)
            ->whereIn('r.payrolty', $allowedPayrollIds)
            ->orderBy('r.Bank')
            ->orderBy('r.Branch')
            ->orderBy('p.WorkNo')
            ->get();

        // Group data by bank and branch
        $groupedData = [];
        $bankBranchTotals = [];
        $grandTotal = 0;
        $grandCount = 0;

        foreach ($data as $row) {
            $bank = $row->Bank;
            $branch = $row->Branch;
            
            if (!isset($groupedData[$bank])) {
                $groupedData[$bank] = [];
            }
            if (!isset($groupedData[$bank][$branch])) {
                $groupedData[$bank][$branch] = [];
                $bankBranchTotals[$bank][$branch] = ['count' => 0, 'amount' => 0];
            }
            
            $groupedData[$bank][$branch][] = $row;

            // Update totals
            $bankBranchTotals[$bank][$branch]['count']++;
            $bankBranchTotals[$bank][$branch]['amount'] += $row->NET_PAY;
            $grandTotal += $row->NET_PAY;
            $grandCount++;
        }

        return [
            'data' => $groupedData,
            'totals' => $bankBranchTotals,
            'grand_total' => $grandTotal,
            'grand_count' => $grandCount
        ];
    }

    /**
     * Add data to PDF
     */
    private function addDataToPdf($pdf, array $bankData): void
    {
        $groupedData = $bankData['data'];
        $bankBranchTotals = $bankData['totals'];
        $grandTotal = $bankData['grand_total'];
        $grandCount = $bankData['grand_count'];

        // Set some defaults
        $pdf->SetFillColor(240, 240, 240); // Light gray for headers
        $pdf->SetDrawColor(128, 128, 128); // Medium gray for borders

        // Loop through banks
        foreach ($groupedData as $bank => $branches) {
            // Bank header
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->SetFillColor(220, 230, 241); // Light blue for bank headers
            $pdf->Cell(190, 8, $bank, 1, 1, 'L', true);
            $pdf->SetFillColor(240, 240, 240); // Reset to light gray
            
            // Loop through branches
            foreach ($branches as $branch => $employees) {
                // Branch header
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(190, 7, 'Branch: ' . $branch, 1, 1, 'L', true);
                
                // Column headers
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Cell(20, 6, 'Work No', 1, 0, 'C', true);
                $pdf->Cell(60, 6, 'Employee Name', 1, 0, 'C', true);
                $pdf->Cell(70, 6, 'Account Number', 1, 0, 'C', true);
                $pdf->Cell(40, 6, 'Net Pay', 1, 1, 'C', true);
                
                // Employee data
                $pdf->SetFont('Arial', '', 9);
                $rowColor = false;
                
                foreach ($employees as $employee) {
                    // Alternate row colors for better readability
                    $pdf->SetFillColor($rowColor ? 255 : 248, $rowColor ? 255 : 248, $rowColor ? 255 : 248);
                    
                    $pdf->Cell(20, 6, $employee->WorkNo, 1, 0, 'L', $rowColor);
                    $pdf->Cell(60, 6, $employee->Name, 1, 0, 'L', $rowColor);
                    $pdf->Cell(70, 6, $employee->AccountNo, 1, 0, 'L', $rowColor);
                    $pdf->Cell(40, 6, number_format($employee->NET_PAY, 2), 1, 1, 'R', $rowColor);
                    
                    $rowColor = !$rowColor; // Toggle row color
                }
                
                // Branch total
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->SetFillColor(230, 230, 230); // Slightly darker gray for totals
                $pdf->Cell(150, 7, 'Branch Total: ' . $bankBranchTotals[$bank][$branch]['count'], 1, 0, 'R', true);
                $pdf->Cell(40, 7, number_format($bankBranchTotals[$bank][$branch]['amount'], 2), 1, 1, 'R', true);
                $pdf->Ln(2); // Add a small space between branches
            }
            
            // Bank total
            $bankTotalCount = array_sum(array_column($bankBranchTotals[$bank], 'count'));
            $bankTotalAmount = array_sum(array_column($bankBranchTotals[$bank], 'amount'));
            
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetFillColor(200, 220, 230); // Different blue for bank totals
            $pdf->Cell(150, 7, 'Bank Total: ' . $bankTotalCount, 1, 0, 'R', true);
            $pdf->Cell(40, 7, number_format($bankTotalAmount, 2), 1, 1, 'R', true);
            $pdf->Ln(5); // Add more space between banks
        }

        // Grand total
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetFillColor(180, 200, 220); // Darker blue for grand total
        $pdf->Cell(150, 8, 'Grand Total: ' . $grandCount, 1, 0, 'R', true);
        $pdf->Cell(40, 8, number_format($grandTotal, 2), 1, 1, 'R', true);
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
}


if (!class_exists('BankAdvicePDF')) {
    class BankAdvicePDF extends \FPDF
    {
        private $schoolDetails;
        private $logoPath;
        private $period;

        public function __construct($orientation, $unit, $size, $schoolDetails, $logoPath, $period)
        {
            parent::__construct($orientation, $unit, $size);
            $this->schoolDetails = $schoolDetails;
            $this->logoPath = $logoPath;
            $this->period = $period;
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
            $this->Cell(0, 10, 'Bank Advice Report - ' . $this->period, 0, 1, 'C');
            $this->Ln(2);
        }

        // Footer
        public function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->SetTextColor(100, 100, 100); // Dark grey
            $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
            $this->SetX(10);
            $this->Cell(0, 10, 'Generated on: ' . date('Y-m-d H:i:s'), 0, 0, 'L');
        }
    }
}