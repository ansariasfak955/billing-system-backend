<?php 

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class PurchaseReceiptFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function purchaseReceipt($id){
        return $this->where('id', $id);
    }
    public function search($search){
        return $this->where(function($q) use ($search)
        {
            return $q->where('payment_option', 'like','%'.$search.'%')->orWhereHas('invoice', function($q) use ($search){
                   $q->where('reference_number',  'like','%'.$search.'%')->orWhereHas('supplier', function($q) use ($search){
                   $q->where('legal_name',  'like','%'.$search.'%');
                });
            });
        });
    }
    // public function paymentOption($payment_option){
    //     return $this->where('payment_option', 'like', '%'.$payment_option.'%');
    // }
    public function referenceNumber($referenceNumber){
        return $this->WhereHas('invoice', function($q) use ($referenceNumber){
            $q->where('reference_number',  'like','%'.$referenceNumber.'%');
        });
    }
    public function supplierName($supplierName){
        return $this->WhereHas('invoice', function($q) use ($supplierName){
            $q->where('name',  'like','%'.$supplierName.'%');
        });
    }
    public function paymentStartDate($date)
    {
        $startDate = \Carbon\Carbon::parse($date);
        return $this->whereDate('payment_date', '>=', $startDate->format('Y-m-d'));
    }
    public function paymentEndDate($date)
    {
        $endDate = \Carbon\Carbon::parse($date);
        return $this->whereDate('payment_date', '<=', $endDate->format('Y-m-d'));
    }
    public function expStartDate($date)
    {
        $startDate = \Carbon\Carbon::parse($date);
        return $this->whereDate('expiration_date', '>=', $startDate->format('Y-m-d'));
    }
    public function expEndDate($date)
    {
        $endDate = \Carbon\Carbon::parse($date);
        return $this->whereDate('expiration_date', '<=', $endDate->format('Y-m-d'));
    }
    public function amount($amount)
    {
        return $this->where('amount', $amount);
    }
    public function paidBy($client_name)
    {
        return $this->whereHas('client', function($q) use ($client_name){
            $q->where('name', 'LIKE', '%'.$client_name.'%');
        });
    }
    public function paymentOption($options)
    {
       $optionsArr = explode(',', $options);
       return $this->whereIn('payment_option', $optionsArr);
    }
}
