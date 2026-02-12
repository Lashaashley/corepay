<?php


namespace App\Http\Controllers;

use App\Models\Pperiod;

use Illuminate\Http\Request;

class PaytrackerController extends Controller
{
   public function index()
    {
        

        $period = Pperiod::where('sstatus', 'Active')->first();

    return view('students.papprove', [
            'month' => $period->mmonth ?? '',
            'year'  => $period->yyear ?? ''
        ]);
    }
}
