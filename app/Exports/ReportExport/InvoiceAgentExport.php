<?php

namespace App\Exports\ReportExport;

use App\Models\InvoiceTable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class InvoiceAgentExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $invoiceAgentsExports;
    public function __construct($invoiceAgentsExports){
        $this->invoiceAgentsExports = $invoiceAgentsExports;
    }
       public function view(): View
    {
        $invoiceAgentsExports = $this->invoiceAgentsExports;
        // dd($invoiceAgentsExports);
        return view('exports.reports.invoiceAgent', compact('invoiceAgentsExports'));
    }
}
