<?php

namespace App\Imports;

use App\Models\ClientAsset;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class ClientAssetImport implements ToModel, WithHeadingRow
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
        $table = 'company_'.$this->company_id.'_client_assets';
        $request = $this->request;
        ClientAsset::setGlobalTable($table); 
        $row['reference'] = $request->reference;

        if(@$row['id']){
            unset($row['id']);
        }

        if(!@$row['subject_to_maintenance']){
            unset($row['subject_to_maintenance']);
        }
        if($request->subject_to_maintenance){
            $row['subject_to_maintenance']= $request->subject_to_maintenance;
        }

        $row['reference_number'] = get_client_asset_latest_ref_number($this->company_id, $request->reference, 1);
        return new ClientAsset($row);
    }
}
