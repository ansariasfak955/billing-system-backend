<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
    	'reference',
		'name',
		'description',
		'base_price',
		'quantity',
		'discount',
		'tax',
		'income_tax',
		'type'
    ];

    protected static $globalTable = 'items' ;

    public function getTable() {
        return self::$globalTable ;
    }
    
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
}
