<?php

namespace App\Exports;

use App\Models\PurchaseTable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PurchaseOrderExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $purchaseOrders;
    public function __construct($purchaseOrders){
        $this->purchaseOrders = $purchaseOrders;
    }
       public function view(): View
    {
        $purchaseOrders = $this->purchaseOrders;
        dd($purchaseOrders);
        return view('exports.purchaseOrder', compact('purchaseOrders'));
    }
}
