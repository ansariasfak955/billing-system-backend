<?php

namespace App\Imports;

use App\Models\ClientAsset;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class ClientAssetImport implements ToModel, WithHeadingRow
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
        $table = 'company_'.$this->company_id.'_client_assets';
        ClientAsset::setGlobalTable($table); 
        $row['reference_number'] = get_client_asset_latest_ref_number($this->company_id, 'ast', 1);
        return new ClientAsset($row);
    }
}
