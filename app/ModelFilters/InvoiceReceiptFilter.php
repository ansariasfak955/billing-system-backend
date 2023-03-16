<?php 

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class InvoiceReceiptFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];
    public function invoiceReceipt($id){
        return $this->where('id', $id);
    }
    public function search($search){
        return $this->where(function($q) use ($search)
        {
            return $q->where('payment_option', 'like','%'.$search.'%')->orWhereHas('invoice', function($q) use ($search){
                $q->where('reference_number',  'like','%'.$search.'%')->orWhere('reference',  'like','%'.$search.'%')
                ->orWhereHas('client', function($q) use ($search){
                    $q->where('legal_name',  'like','%'.$search.'%');
                });
            });
        });
    }
    public function reference($reference)
    {
        return $this->WhereHas('invoice', function($q) use ($reference){
            $q->where('reference',  'like','%'.$reference.'%');
        });
    }
    public function referenceNumber($referenceNumber)
    {
        return $this->WhereHas('invoice', function($q) use ($referenceNumber){
            $q->where('reference_number',  'like','%'.$referenceNumber.'%');
        });
    }
    public function clientName($client_name)
    {
        return $this->whereHas('invoice', function($q) use ($client_name){
            $q->whereHas('client', function($q) use ($client_name){
                $q ->where('legal_name', 'LIKE', '%'.$client_name.'%');
            });   
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
    public function year($year)
    {
        return $this->whereYear('created_at',$year);
    }

}
