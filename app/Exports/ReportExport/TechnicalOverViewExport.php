<?php

namespace App\Exports\ReportExport;

use App\Models\TechnicalIncident;
use App\Models\TechnicalTable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TechnicalOverViewExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $technicalOverviewExports;
    public function __construct($technicalOverviewExports){
        $this->technicalOverviewExports = $technicalOverviewExports;
    }
       public function view(): View
    {
        $technicalOverviewExports = $this->technicalOverviewExports;
        // dd($technicalOverviewExports);
        return view('exports.reports.technicalOverview', compact('technicalOverviewExports'));
    }
}
