<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $appends = ['stock', 'virtual_stock', 'minimum_stock', 'amount'];

    public function getAmountAttribute(){
        if(isset($this->attributes['price'])){

            $basePrice = $this->attributes['price'];
            $discount = isset($this->attributes['discount'])
            ? $this->attributes['discount'] : 0;
            $amount = ($basePrice - ($basePrice * $discount / 100)) ;
            return $amount;
        }
    }

    protected $fillable = ['name', 'price', 'reference', 'reference_number', 'purchase_price', 'barcode', 'image', 'description', 'private_comments', 'created_from', 'purchase_margin', 'sales_margin', ' discount', 'minimum_price', 'tax', 'images'];

    protected static $globalTable = 'products';

    public function getTable() {
        return self::$globalTable ;
    }

    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }

    public function product_attachments(){
        return $this->hasMany(ProductAttachment::class, 'product_id')->where('type' , attachment);
    }

    public function product_images(){
        return $this->hasMany(ProductAttachment::class, 'product_id')->where('type' , image);
    }
    
    public function getImageAttribute()
    {
        if ($this->attributes['image']) {
            return url('/storage').'/products/images/'.$this->attributes['image'];
        } else {
            return 'https://via.placeholder.com/400/fef4d0/060062&text=Not%20Found';
        }
    }

    public function getStockAttribute()
    {
        $table = $this->getTable();
        $company_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
        $product_stock = get_product_stock($company_id, $this->attributes['id']);
        if ($product_stock != NULL) {
            return $product_stock->stock;
        }
        return '';
    }

    public function getVirtualStockAttribute()
    {
        $table = $this->getTable();
        $company_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
        $product_stock = get_product_stock($company_id, $this->attributes['id']);
        if ($product_stock != NULL) {
            return $product_stock->virtual_stock;
        }
        return '';
    }

    public function getMinimumStockAttribute()
    {
        $table = $this->getTable();
        $company_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
        $product_stock = get_product_stock($company_id, $this->attributes['id']);
        if ($product_stock != NULL) {
            return $product_stock->minimum_stock;
        }
        return '';
    }
}