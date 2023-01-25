<?php

namespace App\Exports;

use App\Models\PurchaseReceipt;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PurchaseReceiptExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $purchaseReceipts;
    public function __construct($purchaseReceipts){
        $this->purchaseReceipts = $purchaseReceipts;
    }
       public function view(): View
    {
        $purchaseReceipts = $this->purchaseReceipts;
        dd($purchaseReceipts);
        return view('exports.purchaseReceipt', compact('purchaseReceipts'));
    }
}
