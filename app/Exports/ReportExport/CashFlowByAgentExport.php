<?php

namespace App\Exports\ReportExport;

use App\Models\InvoiceTable;
use App\Models\PurchaseTable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CashFlowByAgentExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $cashflowAgentExports;
    public function __construct($cashflowAgentExports){
        $this->cashflowAgentExports = $cashflowAgentExports;
    }
       public function view(): View
    {
        $cashflowAgentExports = $this->cashflowAgentExports;
        // dd($cashflowAgentExports);
        return view('exports.reports.cashFlowAgent', compact('cashflowAgentExports'));
    }
}
