<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImport implements ToModel, WithHeadingRow
{   
    private $company_id, $request;
    public function __construct($company_id, $request){
        $this->company_id = $company_id;
        $this->request = $request;
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
        $request = $this->request;
        Product::setGlobalTable($table);
        $row['reference'] = $request->reference;
        
        if($request->product_category_id){
            $row['product_category_id']= $request->product_category_id;
        }else{
            if(isset($row['product_category_id'])){
                unset($row['product_category_id']);
            }
        }

        if($request->manage_stock){
            $row['manage_stock']= $request->manage_stock;
        }else{
            if(isset($row['manage_stock'])){
                unset($row['manage_stock']);
            }
        }

        if($request->tax){
            $row['tax']= $request->tax;
        }else{
            if(isset($row['tax'])){
                unset($row['tax']);
            }
        }

        if($request->is_active){
            $row['is_active']= $request->is_active;
        }else{
            if(isset($row['is_active'])){
                unset($row['is_active']);
            }
        }

        if($request->is_promotional){
            $row['is_promotional']= $request->is_promotional;
        }else{
            if(isset($row['is_promotional'])){
                unset($row['is_promotional']);
            }
        }

        $row['reference_number'] = get_product_latest_ref_number($this->company_id, $request->reference, 1);
        return new Product($row);
    }
}
