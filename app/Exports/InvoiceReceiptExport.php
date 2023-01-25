<?php

namespace App\Exports;

use App\Models\InvoiceReceipt;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class InvoiceReceiptExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $invoiceReceipts;
    public function __construct($invoiceReceipts){
        $this->invoiceReceipts = $invoiceReceipts;
    }
       public function view(): View
    {
        $invoiceReceipts = $this->invoiceReceipts;
        return view('exports.invoiceReceipt', compact('invoiceReceipts'));
    }
}
