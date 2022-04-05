<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientSpecialPrice extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
    	"client_id",
		"product_id",
		"purchase_price",
		"sales_price",
		"purchase_margin",
		"sales_margin",
		"discount",
		"special_price"
    ];

    protected static $globalTable = 'clients' ;

    public function getTable() {
        return self::$globalTable ;
    }
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
}
