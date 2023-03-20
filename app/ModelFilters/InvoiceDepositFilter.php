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
    public function client($clientId){
        return $this->where('client_id', $clientId);
    }
    public function paymentOption($paymentOption)
    {
        return $this->whereHas('payment_options', function($q) use ($paymentOption){
            $q->where('name', 'LIKE', '%'.$paymentOption.'%');
        });
       
    }
    public function year($year)
    {
        return $this->whereYear('created_at',$year);
    }
    public function endDate($date)
    {
        $endDate = \Carbon\Carbon::parse($date);
        return $this->whereDate('created_at', '<=', $endDate->format('Y-m-d'));
    }

    public function startDate($date)
    {
        $startDate = \Carbon\Carbon::parse($date);
        return $this->whereDate('created_at', '>=', $startDate->format('Y-m-d'));
    }

}
