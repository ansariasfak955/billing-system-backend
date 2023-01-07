<?php 

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class ServiceFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function service($id){
        return $this->where('id', $id);
    }
    public function search($search){
        return $this->where(function($q) use ($search)
        {
            return $q->where('name', 'like', '%'.$request->search.'%')->orWhere('reference', 'like', '%'.$request->search.'%')
            ->orWhere('reference_number', 'like', '%'.$request->search.'%')->orWhere('price', 'like', '%'.$request->search.'%');
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
}