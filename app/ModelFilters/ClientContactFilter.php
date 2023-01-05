<?php 

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class ClientContactFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];
    public function contact($id){
        return $this->where('id', $id);
    }
    public function search($search){
        return $this->where(function($q) use ($search)
        {
            return $q->where('name', 'like', '%'.$search.'%')->orWhere('phone', 'like', '%'.$search.'%')
            ->orWhere('email', 'like', '%'.$search.'%')->orWhereHas('client', function($q) use ($search){
                $q->where('legal_name',  'like','%'.$search.'%');
            });
        });
    }
    public function name($name){
        return $this->where('name', 'LIKE', '%'.$name.'%');
    }
    public function phone($phone){
        return $this->where('phone', 'LIKE', '%'.$phone.'%');
    }
    public function email($email){
        return $this->where('email', 'LIKE', '%'.$email.'%');
    }
}
