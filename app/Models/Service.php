<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'reference', 'purchase_price', 'image', 'description', 'private_comments', 'vat', 'created_from', 'purchase_margin', 'sales_margin', 'discount', 'minimum_price', 'tax', 'images','reference_number'];

    protected static $globalTable = 'services' ;
    protected $appends = ['product_category_name'];

    public function getTable() {
        return self::$globalTable ;
    }
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
    public function getProductCategoryNameAttribute(){
        if(isset( $this->attributes['product_category_id'] )){
            $table = $this->getTable();
            $company_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_product_category_name($company_id, $this->attributes['product_category_id']);
        }
    }

    public function getImageAttribute()
    {
        if ($this->attributes['image']) {
            return url('/storage').'/services/images/'.$this->attributes['image'];
        } else {
            return 'https://via.placeholder.com/400/fef4d0/060062&text=Not%20Found';
        }
    }
    public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }

}
