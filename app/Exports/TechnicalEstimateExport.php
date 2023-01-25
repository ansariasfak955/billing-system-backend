<?php

namespace App\Exports;

use App\Models\TechnicalTable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TechnicalEstimateExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $technicalEstimates;
    public function __construct($technicalEstimates){
        $this->technicalEstimates = $technicalEstimates;
    }
       public function view(): View
    {
        $technicalEstimates = $this->technicalEstimates;
        dd($technicalEstimates);
        return view('exports.technicalEstimate', compact('technicalEstimates'));
    }
}
