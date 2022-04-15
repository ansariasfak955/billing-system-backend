<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientSpecialPrice extends Model
{
    use HasFactory;

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

    protected static $globalTable = 'client_special_prices' ;

    public function getTable() {
        return self::$globalTable ;
    }

    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }

    protected $appends = ['product_name'];

    public function getProductNameAttribute()
    {
        $table = $this->getTable();
        $company_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
        return get_product_name($company_id, $this->attributes['product_id']);
    }
}
