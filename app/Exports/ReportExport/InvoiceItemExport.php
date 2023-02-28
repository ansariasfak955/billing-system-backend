<?php

namespace App\Exports\ReportExport;

use App\Models\InvoiceTable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class InvoiceItemExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $invoiceItemsExports;
    public function __construct($invoiceItemsExports){
        $this->invoiceItemsExports = $invoiceItemsExports;
    }
       public function view(): View
    {
        $invoiceItemsExports = $this->invoiceItemsExports;
        dd($invoiceItemsExports);
        return view('exports.reports.invoiceItem', compact('invoiceItemsExports'));
    }
}
