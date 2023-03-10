<?php

namespace App\Exports\ReportExport;

use App\Models\ConsumptionTax;
use App\Models\InvoiceTable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class TaxSummaryExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $taxes;
    public function __construct($taxes){
        $this->taxes = $taxes;
    }
       public function view(): View
    {
        $taxes = $this->taxes;
        // dd($taxes);
        return view('exports.reports.taxSummary', compact('taxes'));
    }
}
