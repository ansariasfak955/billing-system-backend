<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CatalogExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $products;
    public function __construct($products){
        $this->products = $products;
    }
       public function view(): View
    {
        $products = $this->products;
        return view('exports.product', compact('products'));
    }
}
