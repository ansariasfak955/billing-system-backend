<?php

namespace App\Exports\ReportExport;

use App\Models\PaymentOption;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PaymentOptionExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $paymentOptionExports;
    public function __construct($paymentOptionExports){
        $this->paymentOptionExports = $paymentOptionExports;
    }
       public function view(): View
    {
        $paymentOptionExports = $this->paymentOptionExports;
        // dd($paymentOptionExports);
        return view('exports.reports.cashFlowPaymentOption', compact('paymentOptionExports'));
    }
}
