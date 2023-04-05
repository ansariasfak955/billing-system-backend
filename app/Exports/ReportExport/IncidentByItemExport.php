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
    protected $data,$request;
    public function __construct($data,$request){
        $this->data = $data;
        $this->request = $request;
    }
       public function view(): View
    {
        $data = $this->data;
        $request = $this->request;
        // dd($data);
        return view('exports.reports.incByItem', compact('data','request'));
    }
}
