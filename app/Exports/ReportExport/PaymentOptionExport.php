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
    protected $data;
    public function __construct($data){
        $this->data = $data;
    }
       public function view(): View
    {
        $data = $this->data;
        dd($data);
        return view('exports.reports.cashFlowPaymentOption', compact('data'));
    }
}
