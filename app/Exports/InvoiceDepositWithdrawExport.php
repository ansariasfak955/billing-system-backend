<?php

namespace App\Exports;

use App\Models\Deposit;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class InvoiceDepositWithdrawExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $invoiceDeposits;
    public function __construct($invoiceDeposits){
        $this->invoiceDeposits = $invoiceDeposits;
    }
    public function view(): View
    {
        $invoiceDeposits = $this->invoiceDeposits;
        return view('exports.transactions', compact('invoiceDeposits'));
    }
}
