<?php

namespace App\Exports\ReportExport;

use App\Models\InvoiceTable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class InvoiceClientExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $invoiceClientsExports;
    public function __construct($invoiceClientsExports){
        $this->invoiceClientsExports = $invoiceClientsExports;
    }
       public function view(): View
    {
        $invoiceClientsExports = $this->invoiceClientsExports;
        // dd($invoiceClientsExports);
        return view('exports.reports.invoiceClient', compact('invoiceClientsExports'));
    }
}
