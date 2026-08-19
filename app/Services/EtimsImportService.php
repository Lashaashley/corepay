<?php

namespace App\Services;

use App\Models\Registration;
use App\Models\EtimsInvoice;
use App\Models\PaymentStatus;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EtimsImportService
{
    protected $month; // e.g. "January"
    protected $year;  // e.g. "2026"

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

   
}