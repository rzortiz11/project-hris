<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade\Pdf;
class DownloadPDFController extends Controller
{
    

    public function downloadPDF(Request $request, string $payroll_id)
    {

        $payroll_data = Payroll::find($payroll_id);
        $result = "test";

        $cut_off_from = date('m-d', strtotime($payroll_data->cut_off_from));
        $cut_off_to = date('m-d', strtotime($payroll_data->cut_off_to));

        $filename = $payroll_data->fullname . '-payslip - ' . $cut_off_from . ' to ' . $cut_off_to . '.pdf';

        return view('payslip_template.payslip_pdf', compact('result', 'payroll_data')); // to view display
        $pdf = Pdf::setOption(['dpi' => 150, 'defaultFont' => 'sans-serif'])
        ->loadView('payslip_template.payslip_pdf', compact('result', 'payroll_data'))
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'margin_left' => 0, 
            'margin_right' => 0, 
            'margin_top' => 0, 
            'margin_bottom' => 0,
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

    return $pdf->download($filename);
    }
}
