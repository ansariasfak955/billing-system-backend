<?php 

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class TechnicalTableFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function technicalService($id){
        $this->where('id', $id);
    }

    public function search($search){
        return $this->where(function($q) use ($search)
        {
            return $q->where('reference_number', 'like', '%'.$search.'%')->orWhere('status', 'like', '%'.$search.'%')
            ->orWhere('title', 'like', '%'.$search.'%')->orWhere('reference', 'like', '%'.$search.'%')
            ->orWhereHas('client', function($q) use ($search){
                 $q->where('legal_name',  'like','%'.$search.'%')->orWhere('email',  'like','%'.$search.'%');
             });
        });
    }
    public function status($status)
    {
            $this->where('status', $status);
    }
    public function reference($reference)
    {
        $this->where('reference', $reference);
    }
    public function date($date)
    {
        $this->whereDate('date', $date);
    }
    public function title($title)
    {
        $this->where('title', $title);
    }
    public function legalName($legalName)
    {
            return $this->whereHas('client', function($q) use ($legalName){
                $q->where('legal_name', $legalName);
            });
    }
    public function email($email)
    {
            return $this->whereHas('client', function($q) use ($email){
                $q->where('email', $email);
            });
    }
}
