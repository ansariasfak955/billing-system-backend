<?php

namespace App\Exports;

use App\Models\InvoiceTable;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportReceipt implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return InvoiceTable::all();
    }
}
