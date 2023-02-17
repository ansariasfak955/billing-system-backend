<?php 

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class ClientCategoryFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function clientCategory($id){
        return $this->where('id', $id);
    }
    public function search($search){
        return $this->where(function($q) use ($search)
        {
            return $q->where('name', 'like', '%'.$search.'%')->orWhere('description', 'like', '%'.$search.'%');
        });
    }
    public function type($type){
        return $this->where('type', 'LIKE', '%'.$type.'%');
    }
}
