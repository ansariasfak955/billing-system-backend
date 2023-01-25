<?php

namespace App\Exports;

use App\Models\SalesEstimate;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SalesEstimateExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $salesEstimates;
    public function __construct($salesEstimates){
        $this->salesEstimates = $salesEstimates;
    }
       public function view(): View
    {
        $salesEstimates = $this->salesEstimates;
        return view('exports.salesEstimate', compact('salesEstimates'));
    }
}
