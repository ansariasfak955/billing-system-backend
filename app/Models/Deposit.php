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

    public function getTable() {
        return self::$globalTable ;
    }
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
    public function modelFilter()
    {
        return $this->provideFilter(\App\ModelFilters\InvoiceDepositFiltersFilter::class);
    }

}
