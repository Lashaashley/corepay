<?php

namespace App\Exports;

use App\Models\Ptype;
use App\Models\Payhouse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NetPayExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $month;
    protected $year;
    protected $pname;
    protected $staff3;
    protected $staff4;
    protected $isLoanOrBalance = false;

    public function __construct($month, $year, $pname, $staff3 = null, $staff4 = null)
    {
        $this->month = $month;
        $this->year = $year;
        $this->pname = $pname;
        $this->staff3 = $staff3;
        $this->staff4 = $staff4;

        // Determine if item is loan/balance type
        $ptype = Ptype::where('cname', $pname)->first();
        if ($ptype) {
            $this->isLoanOrBalance = in_array($ptype->code, ['LOAN', 'BAL']); 
            // <-- adjust codes based on your system
        }
    }

    public function headings(): array
    {
        if ($this->isLoanOrBalance) {
            return [
                'Agent No',
                'Name',
                'Amount',
                'Balance'
            ];
        }

        return [
            'Agent No',
            'Name',
            'Amount'
        ];
    }

    public function collection()
    {
        $allowedPayrollIds = session('allowedPayroll', []);

        $query = Payhouse::from('payhouse as p')
            ->select(
                'p.WorkNo',
                DB::raw("CONCAT(e.FirstName, ' ', e.LastName) AS fullname"),
                'p.tamount'
            )
            ->join('tblemployees as e', 'p.WorkNo', '=', 'e.emp_id')
            ->join('registration as r', 'p.WorkNo', '=', 'r.empid')
            ->where('p.month', $this->month)
            ->where('p.year', $this->year)
            ->where('p.pname', $this->pname)
            ->whereIn('r.payrolty', $allowedPayrollIds);

        // Filter staff range
        if ($this->staff3 && $this->staff4) {
            $query->whereBetween('p.WorkNo', [$this->staff3, $this->staff4]);
        } elseif ($this->staff3) {
            $query->where('p.WorkNo', '>=', $this->staff3);
        } elseif ($this->staff4) {
            $query->where('p.WorkNo', '<=', $this->staff4);
        }

        // Add balance if loan/balance
        if ($this->isLoanOrBalance) {
            $query->leftJoin('payhouse as ph', function ($join) {
                $join->on('p.WorkNo', '=', 'ph.WorkNo')
                    ->where('ph.month', $this->month)
                    ->where('ph.year', $this->year);
            })->addSelect(DB::raw("IFNULL(ph.balance, 0) as balance"));
        }

        $results = $query->orderBy('p.WorkNo')->get();

        // Convert to excel rows + totals
        $rows = collect();
        $totalAmount = 0;
        $totalBalance = 0;

        foreach ($results as $item) {
            $amount = (float) $item->tamount;
            $totalAmount += $amount;

            if ($this->isLoanOrBalance) {
                $balance = (float) ($item->balance ?? 0);
                $totalBalance += $balance;

                $rows->push([
                    $item->WorkNo,
                    $item->fullname,
                    number_format($amount, 2),
                    number_format($balance, 2),
                ]);
            } else {
                $rows->push([
                    $item->WorkNo,
                    $item->fullname,
                    number_format($amount, 2),
                ]);
            }
        }

        // Add totals row
        if ($this->isLoanOrBalance) {
            $rows->push([
                '',
                'TOTAL',
                number_format($totalAmount, 2),
                number_format($totalBalance, 2),
            ]);
        } else {
            $rows->push([
                '',
                'TOTAL',
                number_format($totalAmount, 2),
            ]);
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        // Bold headings
        $sheet->getStyle('A1:Z1')->getFont()->setBold(true);

        // Add report title row above headings
        $sheet->insertNewRowBefore(1, 2);

        $sheet->setCellValue('A1', 'Payroll Report Export');
        $sheet->setCellValue('A2', "Item: {$this->pname} | Period: {$this->month} {$this->year}");

        $sheet->mergeCells('A1:D1');
        $sheet->mergeCells('A2:D2');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);

        // Bold headings row now moved down to row 3
        $sheet->getStyle('A3:Z3')->getFont()->setBold(true);

        // Style totals row (last row)
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A{$lastRow}:Z{$lastRow}")->getFont()->setBold(true);

        // Alignment
        $sheet->getStyle("A3:Z{$lastRow}")
            ->getAlignment()
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        return [];
    }
}
