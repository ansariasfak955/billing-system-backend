<?php

namespace App\Exports\ReportExport;

use App\Models\SalesEstimate;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SalesAgentsExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $agentsSalesExports;
    public function __construct($agentsSalesExports){
        $this->agentsSalesExports = $agentsSalesExports;
    }
       public function view(): View
    {
        $agentsSalesExports = $this->agentsSalesExports;
        return view('exports.reports.agentsSales', compact('agentsSalesExports'));
    }
}
