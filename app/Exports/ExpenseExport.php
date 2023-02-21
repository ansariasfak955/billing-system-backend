<?php

namespace App\Exports;

use App\Models\InvoiceTable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ExpenseExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $items;
    public function __construct($items){
        $this->items = $items;
    }
    public function view(): View
    {
        $items = $this->items;
        // dd($items);
        return view('exports.expenseInvestmntHistory', compact('items'));
    }
}
