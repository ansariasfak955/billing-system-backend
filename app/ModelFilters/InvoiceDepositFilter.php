<?php 

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class InvoiceDepositFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];
    public function deposit($id){
        return $this->where('id', $id);
    }
    public function clientId($clientId){
        return $this->where('client_id', $clientId);
    }
    public function paymentOption($paymentOption)
    {
        return $this->whereHas('payment_options', function($q) use ($paymentOption){
            $q->where('name', 'LIKE', '%'.$paymentOption.'%');
        });
       
    }
   

}
