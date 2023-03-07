<?php

namespace App\Exports\ReportExport;

use App\Models\Supplier;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PurchaseByProviderExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $purchaseByProviders;
    public function __construct($purchaseByProviders){
        $this->purchaseByProviders = $purchaseByProviders;
    }
       public function view(): View
    {
        $purchaseByProviders = $this->purchaseByProviders;
        // dd($purchaseByProviders);
        return view('exports.reports.invoiceByItemEvoluation', compact('purchaseByProviders'));
    }
}
