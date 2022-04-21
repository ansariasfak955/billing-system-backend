<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    use HasFactory;

    protected $fillable = [
    	'product_id',
    	'warehouse',
		'stock',
		'virtual_stock',
		'minimum_stock',
		'location',
    ];

    protected static $globalTable = 'product_stocks' ;

    public function getTable() {
        return self::$globalTable ;
    }

    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
}