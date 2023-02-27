<?php

namespace App\Exports\ReportExport;

use App\Models\TechnicalIncident;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class IncidentByClientExport implements FromView
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
        return view('exports.reports.incidentByClient', compact('invoiceClientsExports'));
    }
}
