<?php 

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class PurchaseTableFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function purchase($id){
        return $this->where('id', $id);
    }

    public function search($search){
        return $this->where(function($q) use ($search)
        {
            return $q->where('reference_number', 'like', '%'.$search.'%')->orWhere('reference', 'like', '%'.$search.'%')
            ->orWhere('title', 'like', '%'.$search.'%')->orWhere('status', 'like', '%'.$search.'%')->orWhere('date', 'like', '%'.$search.'%')
            ->orWhereHas('supplier', function($q) use ($search){
                $q->where('legal_name',  'like','%'.$search.'%');
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
            return $this->whereHas('supplier', function($q) use ($createdByName){
                $q->where('legal_name', 'LIKE', '%'.$createdByName.'%');
            });
    }
    public function supplierName($supplierName)
    {
            return $this->whereHas('supplier', function($q) use ($supplierName){
                $q->where('legal_name', 'LIKE', '%'.$supplierName.'%');
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
    public function supplierCategory($supplierCategory)
    {
        return $this->whereHas('supplier', function($q) use ($supplierCategory){
            $q->whereHas('category', function($q) use ($supplierCategory){
                $q->where('id', $supplierCategory);
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
    public function supplierCategoryNull($supplierCategory)
    {
        return $this->whereHas('supplier', function($q) use ($supplierCategory){
            $q->whereNull('supplier_category')->orWhere('supplier_category',0);  
        });
    }
    public function productCategoryNull($supplierCategory)
    {
        return $this->whereHas('products', function($q) {
            $q->whereHas('product', function($q) {
                $q->whereDoesntHave('productCategory');
            });   
        });
    }
    public function productCategory($supplierCategory)
    {
        return $this->whereHas('products', function($q) use ($supplierCategory){
            $q->whereHas('product', function($q) use ($supplierCategory){
                $q->whereHas('productCategory', function($q) use ($supplierCategory){
                    $q ->where('id', $supplierCategory);
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
    public function expense($productId)
    {
        return $this->whereHas('expenses', function($q) use ($productId){
            $q->where('reference_id', $productId);
        });
    }

}
