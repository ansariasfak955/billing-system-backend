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
    protected $data,$request;
    public function __construct($data,$request){
        $this->data = $data;
        $this->request = $request;
    }
       public function view(): View
    {
        $data = $this->data;
        $request = $this->request;
        // dd($data);
        return view('exports.reports.purchaseByItemEvoluation', compact('data','request'));
    }
}
