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
            return $this->where(function($q) use ($search)
            {
                return $q->where('reference_number', 'like', '%'.$search.'%')->orWhere('reference', 'like', '%'.$search.'%')
                ->orWhere('title', 'like', '%'.$search.'%')->orWhere('status', 'like', '%'.$search.'%')->orWhere('date', 'like', '%'.$search.'%')
                    ->orWhereHas('client', function($q) use ($search){
                    $q->where('legal_name',  'like','%'.$search.'%')->orWhere('email',  'like','%'.$search.'%');;
                });
            });
    }

    public function status($status)
    {
        $statuses = explode(",", $status);
        return $this->whereIn('status', $statuses);
    }
    public function reference($reference)
    {
        return $this->where('reference', $reference);
    }
    public function referenceNumber($referenceNumber)
    {
        return $this->where('reference_number', 'LIKE', '%'.$referenceNumber.'%');
    }
    public function title($title)
    {
        return $this->where('title', 'LIKE', '%'.$title.'%');
    }
    public function date($date)
    {
        return $this->whereDate('date', $date);
    }
    public function createdByName($createdByName)
    {
        return $this->whereHas('client', function($q) use ($createdByName){
            $q->where('legal_name', $createdByName);
        });
    }
    public function clientName($client_name)
    {
        return $this->whereHas('client', function($q) use ($client_name){
            $q->where('legal_name', 'LIKE', '%'.$client_name.'%');
        });
    }
    public function endDate($date)
    {
        $endDate = \Carbon\Carbon::parse($date);
        return $this->whereDate('date', '<=', $endDate->format('Y-m-d'));
    }

    public function startDate($date)
    {
        $startDate = \Carbon\Carbon::parse($date);
        return $this->whereDate('date', '>=', $startDate->format('Y-m-d'));
    }
    public function year($year)
    {
        return $this->whereYear('created_at',$year);
    }
    public function clientCategory($clientCategory)
    {
        return $this->whereHas('client', function($q) use ($clientCategory){
            $q->whereHas('category', function($q) use ($clientCategory){
                $q ->where('id', $clientCategory);
            });  
        });
    }
    public function clientId($clientId)
    {
        return $this->whereHas('client', function($q) use ($clientId){
            $q ->where('client_id', $clientId);
        });
    }
    public function productId($productId)
    {
        return $this->whereHas('item', function($q) use ($productId){
            $q->where('reference_id', $productId);
        });
    }
    public function productType($productType)
    {
        return $this->whereHas('items', function($q) use ($productType){
            $q->where('reference', $productType);
        });
    }
    public function setUsPid($agentId)
    {
        return $this->where('agent_id', $agentId);
    }
    public function paymentOption($payment_option)
    {
        return $this->whereHas('payment_options', function($q) use ($payment_option){
            $q ->where('name', $payment_option);
        });
    }
    public function paid($paid)
    {
        return $this->where('set_as_paid', $paid);
    }

}
