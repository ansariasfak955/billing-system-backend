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
    protected $services;
    public function __construct($services){
        $this->services = $services;
    }
       public function view(): View
    {
        $services = $this->services;
        return view('exports.invoiceReceipt', compact('services'));
    }
}
