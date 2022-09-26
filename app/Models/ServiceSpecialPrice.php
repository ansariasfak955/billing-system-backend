<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceSpecialPrice extends Model
{
    use HasFactory;

    protected $fillable = [
    	"client_id",
		"service_id",
		"purchase_price",
		"sales_price",
		"purchase_margin",
		"sales_margin",
		"discount",
		"special_price",
        'type'
    ];

    protected static $globalTable = 'service_special_prices' ;

    public function getTable() {
        return self::$globalTable ;
    }

    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }

    protected $appends = ['service_name' , 'client_name'];

    public function getServiceNameAttribute()
    {
        $table = $this->getTable();
        $company_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
        return get_service_name($company_id, $this->attributes['service_id']);
    }
     public function getClientNameAttribute(){
        
        if(isset( $this->attributes['client_id'] )){
            $table = $this->getTable();
            $client_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_client_name($client_id, $this->attributes['client_id']);
        }
    }
}
