<?php

namespace App\Exports;

use App\Models\Assets;
use App\Models\ClientAsset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AssetsExport implements FromCollection, WithHeadings
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
        $table = 'company_'.$this->company_id.'_client_assets';
        ClientAsset::setGlobalTable($table); 
        return  ClientAsset::get($this->headings);
    }
}
