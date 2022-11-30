<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImport implements ToModel, WithHeadingRow
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
        $table = 'company_'.$this->company_id.'_products';
        Product::setGlobalTable($table);
        $row['reference_number'] = get_product_latest_ref_number($this->company_id, 'PRO', 1);
        return new Product($row);
    }
}
