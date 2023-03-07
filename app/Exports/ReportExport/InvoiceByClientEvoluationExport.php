<?php

namespace App\Exports\ReportExport;

use App\Models\Client;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class InvoiceByClientEvoluationExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $invoiceByClients;
    public function __construct($invoiceByClients){
        $this->invoiceByClients = $invoiceByClients;
    }
       public function view(): View
    {
        $invoiceByClients = $this->invoiceByClients;
        // dd($invoiceByClients);
        return view('exports.reports.invoiceByClientEvoluation', compact('invoiceByClients'));
    }
}
