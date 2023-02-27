<?php

namespace App\Exports\ReportExport;

use App\Models\TechnicalIncident;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class IncidentsByAgentExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $incidentsAgentsExports;
    public function __construct($incidentsAgentsExports){
        $this->incidentsAgentsExports = $incidentsAgentsExports;
    }
       public function view(): View
    {
        $incidentsAgentsExports = $this->incidentsAgentsExports;
        // dd($incidentsAgentsExports);
        return view('exports.reports.incidentsByAgent', compact('incidentsAgentsExports'));
    }
}
