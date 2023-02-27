<?php

namespace App\Exports\ReportExport;

use App\Models\SalesEstimate;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SalesClientExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $clientSalesExports;
    public function __construct($clientSalesExports){
        $this->clientSalesExports = $clientSalesExports;
    }
       public function view(): View
    {
        $clientSalesExports = $this->clientSalesExports;
        // dd($clientSalesExports);
        return view('exports.reports.clientSales', compact('clientSalesExports'));
    }
}
