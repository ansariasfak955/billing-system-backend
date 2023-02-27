<?php

namespace App\Exports\ReportExport;

use App\Models\PurchaseTable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PurchaseItemExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $purchaseItemExports;
    public function __construct($purchaseItemExports){
        $this->purchaseItemExports = $purchaseItemExports;
    }
       public function view(): View
    {
        $purchaseItemExports = $this->purchaseItemExports;
        // dd($purchaseItemExports);
        return view('exports.reports.purchaseItem', compact('purchaseItemExports'));
    }
}
