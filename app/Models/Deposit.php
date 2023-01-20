<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;

class Deposit extends Model
{
    use HasFactory,Filterable;

    protected $fillable = [
        'concept',
        'date',
        'payment_option',
        'amount',
        'paid_by',
        'type',
        'client_id'
    ];

    protected static $globalTable = 'deposits' ;
    protected $appends = ['paid_by_name','payment_option_name'];
    public function getTable() {
        return self::$globalTable ;
    }
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
    public function getPaidByNameAttribute(){
        
        if(isset( $this->attributes['created_by'] )){
            $table = $this->getTable();
            $createdby = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_user_name($createdby, $this->attributes['created_by']);
        }
    }
    public function getPaymentOptionNameAttribute(){
        
        if(isset( $this->attributes['payment_option'] )){
            $table = $this->getTable();
            $createdby = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_payment_option_name($createdby, $this->attributes['payment_option']);
        }
    }
    public function modelFilter()
    {
        return $this->provideFilter(\App\ModelFilters\InvoiceDepositFiltersFilter::class);
    }

}
