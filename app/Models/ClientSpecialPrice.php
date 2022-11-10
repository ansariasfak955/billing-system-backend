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
		"special_price",
        'type',
        'product_type'
    ];

    protected static $globalTable = 'client_special_prices' ;

    public function getTable() {
        return self::$globalTable ;
    }

    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }

    protected $appends = ['product_name' , 'client_name'];

    public function getProductNameAttribute()
    {
        $table = $this->getTable();
        $company_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
        if($this->attributes['product_type'] == 'service'){
            return get_service_name($company_id, $this->attributes['product_id']);
        }else{

            return get_product_name($company_id, $this->attributes['product_id']);
        }
    }
     public function getClientNameAttribute(){
        
        if(isset( $this->attributes['client_id'] )){
            $table = $this->getTable();
            $client_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_client_name($client_id, $this->attributes['client_id']);
        }
    }
}
