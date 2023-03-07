<?php

namespace App\Exports\ReportExport;

use App\Models\Product;
use App\Models\Service;
use App\Models\ExpenseAndInvestment;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class PurchasesByItemExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $purchaseByItems;
    public function __construct($purchaseByItems){
        $this->purchaseByItems = $purchaseByItems;
    }
       public function view(): View
    {
        $purchaseByItems = $this->purchaseByItems;
        // dd($purchaseByItems);
        return view('exports.reports.purchaseByItemEvoluation', compact('purchaseByItems'));
    }
}
