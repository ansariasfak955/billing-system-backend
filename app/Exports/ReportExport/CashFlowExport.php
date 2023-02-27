<?php

namespace App\Exports\ReportExport;

use App\Models\InvoiceTable;
use App\Models\PurchaseTable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CashFlowExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $cashFlowExports;
    public function __construct($cashFlowExports){
        $this->cashFlowExports = $cashFlowExports;
    }
       public function view(): View
    {
        $cashFlowExports = $this->cashFlowExports;
        // dd($cashFlowExports);
        return view('exports.reports.cashFlow', compact('cashFlowExports'));
    }
}
