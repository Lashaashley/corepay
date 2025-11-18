<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StaffReportService
{
    protected $schoolDetails;
    protected $logoPath;

    public function __construct()
    {
        // Get school details from database using Laravel's DB
        $this->schoolDetails = DB::table('cstructure')->first();
        
        // Get logo path
        $logoFile = $this->schoolDetails->logo ?? 'default.jpg';
        $this->logoPath = storage_path('app/public/students/' . $logoFile);
        
        if (!file_exists($this->logoPath)) {
            $this->logoPath = storage_path('app/public/students/default.png');
        }
    }

    public function generateFullStaffReport()
    {
        // ✅ Include FPDF only once and check if class exists
        if (!class_exists('FPDF')) {
            require_once base_path('fpdf/fpdf.php');
        }
        
        $pdf = new StaffReportPDF('L', 'mm', 'A4', $this->schoolDetails, $this->logoPath);
        $pdf->AliasNbPages();
        $pdf->SetMargins(10, 40, 10);
        $pdf->SetAutoPageBreak(TRUE, 15);
        $pdf->setHeading("Overall Staff Report");
        $pdf->AddPage();
        
        // Get employee data using Laravel's Query Builder
        $employees = $this->getEmployeeData();
        
        // Group by department
        $groupedByDepartment = $employees->groupBy('DepartmentName');
        
        $employeeCount = 0;
        
        foreach ($groupedByDepartment as $department => $deptEmployees) {
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 10, $department, 0, 1, 'L');
            
            $header = ['Full Name', 'Work Number', 'Phone', 'Branch', 'Designation', 'Section', 'Payroll Type', 'Service Period'];
            
            // Convert to array format expected by your PDF class
            $deptData = $deptEmployees->map(function($emp) {
                return (array) $emp;
            })->toArray();
            
            $pdf->EmployeeTable($header, $deptData);
            
            $pdf->Ln(3);
            $employeeCount += $deptEmployees->count();
        }
        
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, "Total Employees: $employeeCount", 0, 1, 'C');
        
        return $pdf->Output('S');
    }

    protected function getEmployeeData()
    {
        return DB::table('tblemployees')
            ->select(
                'tblemployees.*',
                'tbldepartments.DepartmentName',
                DB::raw('COALESCE(stafftypes.Desig, "Not Found") AS Desig'),
                DB::raw('CASE WHEN tblemployees.brid = 0 THEN "Overall" ELSE branches.branchname END AS brname'),
                DB::raw('COALESCE(sections.secname, "Not Found") AS roleName'),
                DB::raw('COALESCE(prolltypes.pname, "Not Found") AS pname'),
                // Use employee's existing role field if available
                'tblemployees.role as employeeRole'
            )
            ->join('tbldepartments', 'tblemployees.Department', '=', 'tbldepartments.id')
            ->leftJoin('stafftypes', 'tblemployees.desigid', '=', 'stafftypes.ID')
            ->leftJoin('sections', 'tblemployees.sectid', '=', 'sections.ID')
            ->leftJoin('branches', 'tblemployees.brid', '=', 'branches.ID')
            ->leftJoin('registration', 'tblemployees.emp_id', '=', 'registration.empid')
            ->leftJoin('prolltypes', 'registration.payrolty', '=', 'prolltypes.ID')
            ->where('tblemployees.emp_id', '!=', '1')
            ->where('tblemployees.Status', 'ACTIVE')
            ->orderBy('tbldepartments.DepartmentName', 'ASC')
            ->orderByRaw('FIELD(tblemployees.role, "HOD") DESC')
            ->orderBy('tblemployees.FirstName', 'ASC')
            ->get();
    }
}

// ✅ Only declare the class if it doesn't exist
if (!class_exists('StaffReportPDF')) {
    /**
     * Custom PDF class for staff reports
     */
    class StaffReportPDF extends \FPDF
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

        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->SetTextColor(100, 100, 100); // Dark grey
            $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
            $this->SetX(10);
            $this->Cell(0, 10, 'Generated on: ' . date('Y-m-d H:i:s'), 0, 0, 'L');
        }

        function EmployeeTable($header, $data) {
            $this->SetFillColor(240, 240, 240);
            $this->SetTextColor(0);
            $this->SetDrawColor(180, 180, 180);
            $this->SetLineWidth(.3);
            $this->SetFont('Arial', 'B', 10);

            // Calculate column widths
            $w = array(60, 25, 25, 45, 30, 30, 30, 30);
            
            // Header
            for($i=0; $i<count($header); $i++)
                $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
            $this->Ln();
            
            // Data
            $this->SetFont('Arial', '', 9);
            $this->SetFillColor(255, 255, 255);
            $fill = false;
            foreach($data as $row) {
                // FIX: Handle date parsing safely
                $servicePeriodFormatted = 'Invalid date';
                if (!empty($row['dateemp'])) {
                    try {
                        $dateEmp = Carbon::createFromFormat('d F Y', $row['dateemp']);
                        if ($dateEmp !== false) {
                            $currentDate = Carbon::now();
                            $servicePeriod = $dateEmp->diff($currentDate);
                            $servicePeriodFormatted = $servicePeriod->y . ' years, ' . $servicePeriod->m . ' months';
                        }
                    } catch (\Exception $e) {
                        $servicePeriodFormatted = 'Date error';
                    }
                }
                
                if($fill) {
                    $this->SetFillColor(240, 240, 240); // Light gray for odd rows
                } else {
                    $this->SetFillColor(255, 255, 255); // White for even rows
                }
                
                $this->Cell($w[0], 6, $row['FirstName'] . ' ' . $row['LastName'], 'LR', 0, 'L', $fill);
                $this->Cell($w[1], 6, $row['emp_id'], 'LR', 0, 'C', $fill);
                $this->Cell($w[2], 6, $row['Phonenumber'], 'LR', 0, 'L', $fill);
                $this->Cell($w[3], 6, $row['brname'], 'LR', 0, 'L', $fill);
                $this->Cell($w[4], 6, $row['Desig'], 'LR', 0, 'L', $fill);
                $this->Cell($w[5], 6, $row['roleName'], 'LR', 0, 'L', $fill);
                $this->Cell($w[6], 6, $row['pname'], 'LR', 0, 'L', $fill);
                $this->Cell($w[7], 6, $servicePeriodFormatted, 'LR', 0, 'L', $fill);
                $this->Ln();
                $fill = !$fill;
            }
            $this->Cell(array_sum($w), 0, '', 'T');
        }
    }
}