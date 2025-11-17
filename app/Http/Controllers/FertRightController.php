<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class FertRightController extends Controller
{
    public function generate(Request $request)
    {
        $pdf = PDF::loadView("fert");
        return $pdf->stream();

        // return view("fert");
        
    }
}
