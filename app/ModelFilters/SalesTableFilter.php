<?php 

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class SalesTableFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function sales($id){
        $this->where('id', $id);
    }

    public function search($search){
        return $this->where(function($q) use ($search)
        {
            return $q->where('reference_number', 'like', '%'.$search.'%')->orWhere('reference', 'like', '%'.$search.'%')
            ->orWhere('title', 'like', '%'.$search.'%')->orWhere('status', 'like', '%'.$search.'%')->orWhere('date', 'like', '%'.$search.'%')                    
            ->orWhereHas('client', function($q) use ($search){
                $q->where('legal_name',  'like','%'.$search.'%')->orWhere('email',  'like','%'.$search.'%');
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
        return $this->where('reference', 'LIKE', '%'.$reference.'%');
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
                $q->where('legal_name', 'LIKE', '%'.$createdByName.'%');
            });
    }
    public function client($clientId)
    {
        return $this->whereHas('client', function($q) use ($clientId){
            $q ->where('client_id', $clientId);
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
        return $this->whereDate('created_at', '<=', $endDate->format('Y-m-d'));
    }

    public function startDate($date)
    {
        $startDate = \Carbon\Carbon::parse($date);
        return $this->whereDate('created_at', '>=', $startDate->format('Y-m-d'));
    }
    public function clientCategory($clientCategory)
    {
        return $this->whereHas('client', function($q) use ($clientCategory){
            $q->whereHas('category', function($q) use ($clientCategory){
                $q->where('id', $clientCategory);
            });  
        });
    }
    public function year($year)
    {
        return $this->whereYear('created_at',$year);
    }
    public function agent($agentId)
    {
        return $this->where('agent_id', $agentId);
    }
    public function clientCategoryNull($clientCategory)
    {
        return $this->whereHas('client', function($q) use ($clientCategory){
            $q->whereNull('client_category')->orWhere('client_category',0);  
        });
    }
    public function productCategoryNull($clientCategory)
    {
        return $this->whereHas('products', function($q) {
            $q->whereHas('product', function($q) {
                $q->whereDoesntHave('productCategory');
            });   
        });
    }
    public function productCategory($clientCategory)
    {
        return $this->whereHas('products', function($q) use ($clientCategory){
            $q->whereHas('product', function($q) use ($clientCategory){
                $q->whereHas('productCategory', function($q) use ($clientCategory){
                    $q ->where('id', $clientCategory);
                });  
            }); 
        });
    }
    public function product($productId)
    {
        return $this->whereHas('products', function($q) use ($productId){
            $q->where('reference_id', $productId);
        });
    }
    public function service($productId)
    {
        return $this->whereHas('services', function($q) use ($productId){
            $q->where('reference_id', $productId);
        });
    }
}
