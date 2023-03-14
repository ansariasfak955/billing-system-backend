<?php

namespace App\Exports\ReportExport;

use App\Models\InvoiceReceipt;
use App\Models\PurchaseReceipt;
use App\Models\Item;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class OverViewExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $overViewExports;
    public function __construct($overViewExports){
        $this->overViewExports = $overViewExports;
    }
       public function view(): View
    {
        $overViewExports = $this->overViewExports;
        // dd($overViewExports);
        return view('exports.reports.overView', compact('overViewExports'));
    }
}
