<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        "purchase_price",
		"sales_price",
		"purchase_margin",
		"sales_margin",
		"discount",
		"special_price"
    ];

    protected static $globalTable = 'rates' ;

    public function getTable() {
        return self::$globalTable ;
    }
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
}
