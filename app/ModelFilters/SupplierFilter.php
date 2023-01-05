<?php 

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class SupplierFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];
    public function supplier($id){
        return $this->where('id', $id);
    }
    public function search($search)
    {
        return $this->where(function($q) use ($search)
        {
            return $q->where('name', 'like', '%'.$search.'%')->orWhere('legal_name', 'like', '%'.$search.'%')
            ->orWhere('reference_number', 'like', '%'.$search.'%')->orWhere('reference', 'like', '%'.$search.'%')
            ->orWhere('phone_1', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%')
            ->orWhere('tin', 'like', '%'.$search.'%');
        });
    }
    public function name($name){
        return $this->where('name', 'like', '%'.$name.'%');
    }
    public function legalName($legal_name){
        return $this->where('legal_name', 'like', '%'.$legal_name.'%');
    }
    public function referenceNumber($referenceNumber){
        return $this->where('reference_number', 'like', '%'.$referenceNumber.'%');
    }
    public function reference($reference){
        return $this->where('reference', 'like', '%'.$reference.'%');
    }
    public function phone($phone){
        return $this->where('phone_1', 'like', '%'.$phone.'%');
    }
    public function email($email){
        return $this->where('email', 'like', '%'.$email.'%');
    }
}
