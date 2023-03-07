<?php

namespace App\Exports\ReportExport;

use App\Models\InvoiceTable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class InvoiceByItemEvoluationExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $invoiceByItems;
    public function __construct($invoiceByItems){
        $this->invoiceByItems = $invoiceByItems;
    }
       public function view(): View
    {
        $invoiceByItems = $this->invoiceByItems;
        // dd($invoiceByItems);
        return view('exports.reports.invoiceByItemEvoluation', compact('invoiceByItems'));
    }
}
