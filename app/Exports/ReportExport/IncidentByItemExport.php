<?php

namespace App\Exports\ReportExport;

use App\Models\TechnicalTable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class IncidentByItemExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $incidentByItemExports;
    public function __construct($incidentByItemExports){
        $this->incidentByItemExports = $incidentByItemExports;
    }
       public function view(): View
    {
        $incidentByItemExports = $this->incidentByItemExports;
        // dd($incidentByItemExports);
        return view('exports.reports.incByItem', compact('incidentByItemExports'));
    }
}
