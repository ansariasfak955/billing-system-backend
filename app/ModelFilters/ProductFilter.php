<?php 

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class ProductFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function product($id){
        return $this->where('id', $id);
    }

    public function search($search){
        return $this->where(function($q) use ($search)
        {
            return $q->where('name', 'like', '%'.$search.'%')->orWhere('reference_number', 'like', '%'.$search.'%')
            ->orWhere('reference', 'like', '%'.$search.'%')->orWhere('price', 'like', '%'.$search.'%');
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
    public function price($price){
        return $this->where('price', 'LIKE', '%'.$price.'%');
    }
    public function pro($pro){
        return $this->where('id', 'LIKE', '%'.$pro.'%');
    }
}
