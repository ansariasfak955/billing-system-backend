<?php

namespace App\Exports;

use App\Models\Supplier;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PurchaseSupplierExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $suppliers;
    public function __construct($suppliers){
        $this->suppliers = $suppliers;
    }
       public function view(): View
    {
        $suppliers = $this->suppliers;
        return view('exports.supplier', compact('suppliers'));
    }
}
