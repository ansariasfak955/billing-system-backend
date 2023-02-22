<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierSpecialPrice extends Model
{
    use HasFactory;
    protected $fillable = [
        "supplier_id",
        "product_id",
        "purchase_price",
        "sales_price",
        "purchase_margin",
        "sales_margin",
        "discount",
        "type",
        "special_price",
        'product_type'
    ];

    protected static $globalTable = 'supplier_special_prices' ;

    public function getTable() {
        return self::$globalTable ;
    }

    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }

    protected $appends = ['product_name','supplier_name'];

    public function getProductNameAttribute()
    {
        $table = $this->getTable();
        $company_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);

        if($this->attributes['product_type'] == 'SER'){
            return get_service_name($company_id, $this->attributes['product_id']);
        }else{
            return get_product_name($company_id, $this->attributes['product_id']);
        }
    }
    public function getSupplierNameAttribute(){
        
        if(isset( $this->attributes['supplier_id'] )){
            $table = $this->getTable();
            $company = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_supplier_name($company, $this->attributes['supplier_id']);
        }
    }
}
