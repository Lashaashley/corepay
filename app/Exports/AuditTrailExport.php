<?php

namespace App\Exports;

use App\Models\AuditTrail;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AuditTrailExport implements WithMultipleSheets
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function sheets(): array
    {
        return [
            new AuditTrailDetailsSheet($this->filters),
            new AuditTrailSummarySheet($this->filters),
        ];
    }
}

class AuditTrailDetailsSheet implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = AuditTrail::select([
            'audittrail.id',
            'audittrail.user_id',
            'audittrail.action',
            'audittrail.table_name',
            'audittrail.record_id',
            'audittrail.old_values',
            'audittrail.new_values',
            'audittrail.context_data',
            'audittrail.ip_address',
            'audittrail.user_agent',
            'audittrail.created_at',
            'users.name as user_name',
            DB::raw("CONCAT(COALESCE(tblemployees.FirstName, ''), ' ', COALESCE(tblemployees.LastName, '')) as affected_user_name")
        ])
        ->leftJoin('users', 'audittrail.user_id', '=', 'users.id')
        ->leftJoin('tblemployees', 'audittrail.record_id', '=', 'tblemployees.emp_id');

        $this->applyFilters($query);

        return $query->orderBy('audittrail.created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Date & Time',
            'User ID',
            'User Name',
            'Action',
            'Table',
            'Record ID',
            'Affected User',
            'IP Address',
            'Old Values',
            'New Values',
            'Context Data',
            'User Agent'
        ];
    }

    public function map($audit): array
    {
        return [
            $audit->id,
            $audit->created_at,
            $audit->user_id,
            $audit->user_name ?? 'Unknown',
            $audit->action,
            $audit->table_name,
            $audit->record_id,
            !empty(trim($audit->affected_user_name)) ? $audit->affected_user_name : 'N/A',
            $audit->ip_address,
            $audit->old_values,
            $audit->new_values,
            $audit->context_data,
            $audit->user_agent
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E2EFDA']]],
        ];
    }

    public function title(): string
    {
        return 'Audit Details';
    }

    private function applyFilters($query)
    {
        if (!empty($this->filters['user_id'])) {
            $query->where('audittrail.user_id', $this->filters['user_id']);
        }

        if (!empty($this->filters['action'])) {
            $query->where('audittrail.action', $this->filters['action']);
        }

        if (!empty($this->filters['table_name'])) {
            $query->where('audittrail.table_name', $this->filters['table_name']);
        }

        if (!empty($this->filters['record_id'])) {
            $query->where('audittrail.record_id', $this->filters['record_id']);
        }

        if (!empty($this->filters['from_date'])) {
            $query->whereDate('audittrail.created_at', '>=', $this->filters['from_date']);
        }

        if (!empty($this->filters['to_date'])) {
            $query->whereDate('audittrail.created_at', '<=', $this->filters['to_date']);
        }
    }
}

class AuditTrailSummarySheet implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = AuditTrail::select([
            'action',
            DB::raw('COUNT(*) as count'),
            DB::raw('MIN(created_at) as first_occurrence'),
            DB::raw('MAX(created_at) as last_occurrence')
        ])
        ->groupBy('action');

        if (!empty($this->filters['from_date'])) {
            $query->whereDate('created_at', '>=', $this->filters['from_date']);
        }

        if (!empty($this->filters['to_date'])) {
            $query->whereDate('created_at', '<=', $this->filters['to_date']);
        }

        return $query->orderBy('count', 'desc');
    }

    public function headings(): array
    {
        return [
            'Action Type',
            'Total Count',
            'First Occurrence',
            'Last Occurrence'
        ];
    }

    public function map($summary): array
    {
        return [
            $summary->action,
            $summary->count,
            $summary->first_occurrence,
            $summary->last_occurrence
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FFF2CC']]],
        ];
    }

    public function title(): string
    {
        return 'Summary';
    }
}