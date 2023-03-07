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
    protected $stockValuationExports;
    public function __construct($stockValuationExports){
        $this->stockValuationExports = $stockValuationExports;
    }
       public function view(): View
    {
        $stockValuationExports = $this->stockValuationExports;
        // dd($stockValuationExports);
        return view('exports.reports.stockValuation', compact('stockValuationExports'));
    }
}
