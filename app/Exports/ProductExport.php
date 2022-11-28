<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $heading, $data;
    public function __construct($headings, $data){
        $this->headings = $headings;
        $this->data = $data;
    }
    public function headings(): array
    {
        return  $this->headings;
    }
    public function collection()
    {   
        return  $this->data;
    }
}
