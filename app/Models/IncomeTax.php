<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;

class IncomeTax extends Model
{
    use HasFactory,Filterable;
    protected $guarded = ['id', 'created_at', 'updated_at'];
    
    protected static $globalTable = 'income_taxes';

    public function getTable() {
        return self::$globalTable ;
    }

    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
    public function getTaxesAttribute(){
        if(isset($this->attributes['taxes'])){
            return json_decode($this->attributes['taxes']);
        }
    }
    public function modelFilter()
    {
        return $this->provideFilter(\App\ModelFilters\IncomeTaxFilter::class);
    }
}
