<?php

namespace App\Http\Controllers;
use App\Models\Withholding;
use App\Models\Whgroups;
use App\Models\Ptype;
use Exception;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayimportController extends Controller
{
    public function index()
    {
        return view('students.payimport');
    }

}