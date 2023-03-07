<?php

namespace App\Exports\ReportExport;

use App\Models\InvoiceTable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class InvoiceByAgentEvoluationExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $invoiceByagents;
    public function __construct($invoiceByagents){
        $this->invoiceByagents = $invoiceByagents;
    }
       public function view(): View
    {
        $invoiceByagents = $this->invoiceByagents;
        // dd($invoiceByagents);
        return view('exports.reports.invoiceByAgentEvoluation', compact('invoiceByagents'));
    }
}
