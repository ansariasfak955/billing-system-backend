<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SupplierExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $heading, $company_id;
    public function __construct($headings, $company_id){
        $this->headings = $headings;
        $this->company_id = $company_id;
    }
    public function headings(): array
    {
        return  $this->headings;
    }

    public function collection()
    {
        $table = 'company_'.$this->company_id.'_suppliers';
        Supplier::setGlobalTable($table);
        return  Supplier::get($this->headings);
    }
}
