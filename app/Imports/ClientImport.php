<?php

namespace App\Imports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ClientImport implements ToModel, WithHeadingRow
{
    private $company_id,$request;
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
        $table = 'company_'.$this->company_id.'_clients';
        $request = $this->request;
        Client::setGlobalTable($table);
        $row['reference']= $request->reference;
        //unset the empty columns to prevent integrity error

        if(!@$row['client_category']){
            unset($row['client_category']);
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
        if(!@$row['invoice_to']){
            unset($row['invoice_to']);
        }
        if(@$row['id']){
            unset($row['id']);
        }
        if(@$row['ced_ruc']){
            $row['tin']= $row['ced_ruc'];
            unset($row['ced_ruc']);
        }

        if(!@$row['payment_option_id']){unset($row['payment_option_id']);}
        //set the default value
        if($request->client_category){
            $row['client_category']= $request->client_category;
        }

        if($request->agent){
            $row['agent']= $request->agent;
        }

        if($request->rate){
            $row['rate']= $request->rate;
        }

        if($request->payment_option_id){
            $row['payment_option_id']= $request->payment_option_id;
        }

        if($request->payment_terms_id){
            $row['payment_terms_id']= $request->payment_terms_id;
        }

        $row['reference_number'] = get_client_latest_ref_number($this->company_id, $request->reference, 1);
        return new Client($row);
    }
}
