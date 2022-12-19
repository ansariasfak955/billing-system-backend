<?php 

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class InvoiceTableFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function invoice($id){
        $this->where('id', $id);
    }

    public function search($search)
    {
                // return $q->where('reference_number', 'like', '%'.$search.'%')->orWhere('reference', 'like', '%'.$search.'%')
                // ->orWhere('title', 'like', '%'.$search.'%')->orWhere('status', 'like', '%'.$search.'%')->orWhere('date', 'like', '%'.$search.'%');
                // ->orWhereHas('client', function($q) use ($request){
                // $q->where('legal_name',  'like','%'.$search.'%')->orWhere('email',  'like','%'.$search.'%');;
            // });
            return $this->where(function($q) use ($search)
            {
                return $q->where('reference_number', 'like', '%'.$search.'%')->orWhere('reference', 'like', '%'.$search.'%')
                ->orWhere('title', 'like', '%'.$search.'%')->orWhere('status', 'like', '%'.$search.'%')->orWhere('date', 'like', '%'.$search.'%')
                    ->orWhereHas('client', function($q) use ($search){
                    $q->where('legal_name',  'like','%'.$search.'%')->orWhere('email',  'like','%'.$search.'%');;
                });
            });
    }

}
