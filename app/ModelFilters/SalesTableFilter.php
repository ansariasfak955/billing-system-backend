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
        // $this->where('status', 'LIKE', '%'.$status.'%');
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
    public function createdByName($createdByName)
    {
        return $this->where('created_by', 'LIKE', '%'.$createdByName.'%');
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
}
