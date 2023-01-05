<?php 

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class ClientFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function client($id){
        return $this->where('id', $id);
    }
    public function search($search){
        return $this->where(function($q) use ($search)
        {
            return $q->where('name', 'like', '%'.$search.'%')->orWhere('legal_name', 'like', '%'.$search.'%')->orWhere('reference', 'like', '%'.$search.'%')
            ->orWhere('reference_number', 'like', '%'.$search.'%')->orWhere('tin', 'like', '%'.$search.'%')
            ->orWhere('email', 'like', '%'.$search.'%')->orWhere('agent', 'like', '%'.$search.'%')
            ->orWhere('phone_1', 'like', '%'.$search.'%')->orWhere('phone_2', 'like', '%'.$search.'%');
        });
    }
    public function reference($reference){
        return $this->where('reference', 'LIKE', '%'.$reference.'%');
    }
    public function referenceNumber($referenceNumber){
        return $this->where('reference_number', 'LIKE', '%'.$referenceNumber.'%');
    }
    public function legalName($legalName){
        return $this->where('legal_name', 'LIKE', '%'.$legalName.'%');
    }
    public function name($name){
        return $this->where('name', 'LIKE', '%'.$name.'%');
    }
    public function phone($phone){
        return $this->where('phone_1', 'LIKE', '%'.$phone.'%');
    }
    public function email($email){
        return $this->where('email', 'LIKE', '%'.$email.'%');
    }
}
