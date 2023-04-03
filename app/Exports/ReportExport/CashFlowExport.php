<?php

namespace App\Exports\ReportExport;

use App\Models\InvoiceTable;
use App\Models\PurchaseTable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CashFlowExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $data,$overview,$request;
    public function __construct($data,$overview, $request){
        $this->data = $data;
        $this->overview = $overview;
        $this->request = $request;
    }
       public function view(): View
    {
        $data = $this->data; 
        $overview = $this->overview; 
        $request = $this->request; 
        // dd($overview);
        return view('exports.reports.cashFlow', compact('data','overview','request'));
    }
}
