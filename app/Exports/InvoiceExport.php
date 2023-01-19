<?php

namespace App\Exports;

use App\Models\InvoiceTable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class InvoiceExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $invoices;
    public function __construct($invoices){
        $this->invoices = $invoices;
    }
    public function view(): View
    {
        $invoices = $this->invoices;
        return view('exports.invoices', compact('invoices'));
    }
}
