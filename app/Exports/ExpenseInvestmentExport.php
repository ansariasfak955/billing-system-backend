<?php

namespace App\Exports;

use App\Models\ExpenseAndInvestment;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ExpenseInvestmentExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $expenseInvestments;
    public function __construct($expenseInvestments){
        $this->expenseInvestments = $expenseInvestments;
    }
       public function view(): View
    {
        $expenseInvestments = $this->expenseInvestments;
        return view('exports.expenseInvestment', compact('expenseInvestments'));
    }
}
