<?php

namespace App\Imports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class SupplierImport implements ToModel, WithHeadingRow
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
        $table = 'company_'.$this->company_id.'_suppliers';
        $request = $this->request;
        Supplier::setGlobalTable($table);
        $row['reference']= $request->reference;
        if(@$row['reference_number']){
            unset($row['reference_number']);
        }
        $row['reference_number'] = get_supplier_latest_ref_number($this->company_id, $request->reference, 1);
        //unset the empty columns to prevent integrity error
        if(@$row['id'] = ''){
            unset($row['id']);
        }

        if($row['invoice_to'] = ''){
            unset($row['invoice_to']);
        }
        if(!@$row['supplier_category']){
            unset($row['supplier_category']);
        }
        if(!@$row['agent']){
            unset($row['agent']);
        }
        if(!@$row['rate']){
            unset($row['rate']);
        }
        if(!@$row['payment_terms_id']){
            unset($row['payment_terms_id']);
        }

        //set the default value
        if($request->supplier_category){
            $row['supplier_category']= $request->supplier_category;
        }

        if($request->agent){
            $row['agent']= $request->agent;
        }

        if($request->rate){
            $row['rate']= $request->rate;
        }

        if($request->payment_terms_id){
            $row['payment_terms_id']= $request->payment_terms_id;
        }

        $row['reference_number'] = get_Supplier_latest_ref_number($this->company_id, $request->reference, 1);
        return new Supplier($row);
    }
}
