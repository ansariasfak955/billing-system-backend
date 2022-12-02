<?php

namespace App\Imports;

use App\Models\Service;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ServiceImport implements ToModel, WithHeadingRow
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
        $table = 'company_'.$this->company_id.'_services';
        $request = $this->request;
        Service::setGlobalTable($table);
        $row['reference'] = $request->reference;

        if(@$row['id']){
            unset($row['id']);
        }

        if(!@$row['tax']){
            unset($row['tax']);
        }

        if(!@$row['is_active']){
            unset($row['is_active']);
        }

        if(!@$row['is_promotional']){
            unset($row['is_promotional']);
        }


        if($request->tax){
            $row['tax']= $request->tax;
        }

        if($request->is_active){
            $row['is_active']= $request->is_active;
        }

        if($request->is_promotional){
            $row['is_promotional']= $request->is_promotional;
        }

        $row['reference_number'] = get_service_latest_ref_number($this->company_id, $request->reference, 1);
        return new Service($row);
    }
}
