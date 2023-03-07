<?php

namespace App\Exports\ReportExport;

use App\Models\SupplierSpecialPrice;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class OfProfitExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $ofProfits;
    public function __construct($ofProfits){
        $this->ofProfits = $ofProfits;
    }
       public function view(): View
    {
        $ofProfits = $this->ofProfits;
        // dd($ofProfits);
        return view('exports.reports.ofProfit', compact('ofProfits'));
    }
}
