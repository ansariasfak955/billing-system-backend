<?php

namespace App\Exports;

use App\Models\TechnicalIncident;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TechnicalIncidentExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $incidents;
    public function __construct($incidents){
        $this->incidents = $incidents;
    }
       public function view(): View
    {
        $incidents = $this->incidents;
        return view('exports.technicalIncident', compact('incidents'));
    }
}
