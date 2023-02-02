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
    private $expenseHistorys;
    public function __construct($expenseHistorys){
        $this->expenseHistorys = $expenseHistorys;
    }
    public function view(): View
    {
        $expenseHistorys = $this->expenseHistorys;
        return view('exports.expenseInvestmntHistory', compact('expenseHistorys'));
    }
}
