<?php 

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class ClientAssetFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function clientAsset($id){
        return $this->where('id', $id);
    }
    public function search($search){
        return $this->where(function($q) use ($search)
        {
            return $q->where('name', 'like', '%'.$search.'%')->orWhere('reference_number', 'like', '%'.$search.'%')->orWhere('reference', 'like', '%'.$search.'%')
            ->orWhere('identifier', 'like', '%'.$search.'%')->orWhere('serial_number', 'like', '%'.$search.'%')
            ->orWhereHas('client', function($q) use ($search){
                $q->where('legal_name',  'like','%'.$search.'%');
            });
        });
    }
    public function name($name){
        return $this->where('name', 'LIKE', '%'.$name.'%');
    }
    public function reference($reference){
        return $this->where('reference', 'LIKE', '%'.$reference.'%');
    }
    public function referenceNumber($referenceNumber){
        return $this->where('reference_number', 'LIKE', '%'.$referenceNumber.'%');
    }
    public function serialNumber($serialNumber){
        return $this->where('serial_number', 'LIKE', '%'.$serialNumber.'%');
    }
    public function clientName($client_name)
    {
        return $this->whereHas('client', function($q) use ($client_name){
            $q->where('legal_name', 'LIKE', '%'.$client_name.'%');
        });
    }
}
