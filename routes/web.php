<?php

use App\Http\Controllers\WEB\DownloadPDFController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['service' => 'OK']);
});

Route::get('download/payslip/pdf/{payroll_id}', [DownloadPDFController::class, 'downloadPDF'])->name('download.payslip.pdf');