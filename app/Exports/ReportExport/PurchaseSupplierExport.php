<?php

namespace App\Exports\ReportExport;

use App\Models\PurchaseTable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PurchaseSupplierExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $purchaseSupplierExports;
    public function __construct($purchaseSupplierExports){
        $this->purchaseSupplierExports = $purchaseSupplierExports;
    }
       public function view(): View
    {
        $purchaseSupplierExports = $this->purchaseSupplierExports;
        // dd($purchaseSupplierExports);
        return view('exports.reports.purchaseSupplier', compact('purchaseSupplierExports'));
    }
}
