<?php

namespace App\Exports\ReportExport;

use App\Models\SalesEstimate;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SalesItemsExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $itemsSalesExports;
    public function __construct($itemsSalesExports){
        $this->itemsSalesExports = $itemsSalesExports;
    }
       public function view(): View
    {
        $itemsSalesExports = $this->itemsSalesExports;
        return view('exports.reports.itemsSales', compact('itemsSalesExports'));
    }
}
