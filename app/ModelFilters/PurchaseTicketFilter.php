<?php 

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class PurchaseTicketFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function purchaseTicket($id){
        return $this->where('id', $id);
    }

    public function search($search){
        return $this->where(function($q) use ($search)
        {
            return $q->where('reference_number', 'like', '%'.$search.'%')->orWhere('date', 'like', '%'.$search.'%')
            ->orWhere('status', 'like', '%'.$search.'%')->orWhere('payment_date', 'like', '%'.$search.'%')->orWhere('amount', 'like', '%'.$search.'%')
            ->orWhereHas('user', function($q) use ($search){
                $q->where('name',  'like','%'.$search.'%');
            })
            ->orWhereHas('supplier', function($q) use ($search){
                $q->where('legal_name',  'like','%'.$search.'%');
            });
        });
    }
    public function referenceNumber($referenceNumber){
        return $this->where('reference_number', 'like', '%'.$referenceNumber.'%');
    }
    public function status($status){
        $statuses = explode(',', $status);
        return $this->whereIn('status', $statuses);
    }
    public function supplierName($supplierName){
        return $this->WhereHas('supplier', function($q) use ($supplierName){
            $q->where('legal_name',  'like','%'.$supplierName.'%');
        });
    }
    public function employeeName($employeeName){
        return $this->WhereHas('user', function($q) use ($employeeName){
            $q->where('name',  'like','%'.$employeeName.'%');
        });
    }
        public function paymentStartDate($date)
    {
        $startDate = \Carbon\Carbon::parse($date);
        return $this->whereDate('date', '>=', $startDate->format('Y-m-d'));
    }
    public function paymentEndDate($date)
    {
        $endDate = \Carbon\Carbon::parse($date);
        return $this->whereDate('date', '<=', $endDate->format('Y-m-d'));
    }
    public function expStartDate($date)
    {
        $startDate = \Carbon\Carbon::parse($date);
        return $this->whereDate('payment_date', '>=', $startDate->format('Y-m-d'));
    }
    public function expEndDate($date)
    {
        $endDate = \Carbon\Carbon::parse($date);
        return $this->whereDate('payment_date', '<=', $endDate->format('Y-m-d'));
    }
}
