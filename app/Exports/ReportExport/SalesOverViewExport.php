<?php

namespace App\Exports\ReportExport;

use App\Models\SalesEstimate;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SalesOverViewExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $salesOverViewExports;
    public function __construct($salesOverViewExports){
        $this->salesOverViewExports = $salesOverViewExports;
    }
       public function view(): View
    {
        $salesOverViewExports = $this->salesOverViewExports;
        // dd($salesOverViewExports);
        return view('exports.reports.salesOverview', compact('salesOverViewExports'));
    }
}
