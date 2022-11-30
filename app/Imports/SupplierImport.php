<?php

namespace App\Imports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class SupplierImport implements ToModel, WithHeadingRow
{
    private $company_id;
    public function __construct($company_id){
        $this->company_id = $company_id;
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    use Importable;
    public function model(array $row)
    {
        $table = 'company_'.$this->company_id.'_suppliers';
        Supplier::setGlobalTable($table);
            $row['reference_number'] = get_Supplier_latest_ref_number($this->company_id, 'SUP', 1);
            return new Supplier($row);
    }
}
