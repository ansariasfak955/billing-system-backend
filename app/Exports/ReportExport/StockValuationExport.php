<?php

namespace App\Exports\ReportExport;

use App\Models\SupplierSpecialPrice;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class StockValuationExport implements FromView
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
        return view('exports.reports.stockValuation', compact('data','request'));
    }
}
