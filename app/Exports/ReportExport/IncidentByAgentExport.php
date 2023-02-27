<?php

namespace App\Exports\ReportExport;

use App\Models\TechnicalTable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class IncidentByAgentExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $incidentByAgentExports;
    public function __construct($incidentByAgentExports){
        $this->incidentByAgentExports = $incidentByAgentExports;
    }
       public function view(): View
    {
        $incidentByAgentExports = $this->incidentByAgentExports;
        // dd($incidentByAgentExports);
        return view('exports.reports.incByAgent', compact('incidentByAgentExports'));
    }
}
