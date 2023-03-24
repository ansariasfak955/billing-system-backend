<?php

namespace App\Exports\ReportExport;


use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class OfProfitExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $data;
    private $request;
    public function __construct($data, $request){
        $this->data = $data;
        $this->request = $request;
    }
    public function view(): View
    {
        // dd($this->total);
        $data = $this->data;
        $request = $this->request;
        return view('exports.reports.of-profit', compact('data', 'request'));
    }
}
