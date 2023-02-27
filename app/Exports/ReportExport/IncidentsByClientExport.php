<?php

namespace App\Exports\ReportExport;

use App\Models\TechnicalIncident;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class IncidentsByClientExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $incidentByClientExports;
    public function __construct($incidentByClientExports){
        $this->incidentByClientExports = $incidentByClientExports;
    }
       public function view(): View
    {
        $incidentByClientExports = $this->incidentByClientExports;
        // dd($incidentByClientExports);
        return view('exports.reports.incidentsByClient', compact('incidentByClientExports'));
    }
}
