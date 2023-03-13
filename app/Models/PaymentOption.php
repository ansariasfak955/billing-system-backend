<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;

class PaymentOption extends Model
{
    use HasFactory,Filterable;

    protected $fillable = [
        "name",
        "by_default",
        "link_bank_account",
        "description"
    ];

    protected static $globalTable = 'payment_options' ;

    public function getTable() {
        return self::$globalTable ;
    }
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
    public function modelFilter()
    {
        return $this->provideFilter(\App\ModelFilters\PaymentOptionFilter::class);
    }
}
