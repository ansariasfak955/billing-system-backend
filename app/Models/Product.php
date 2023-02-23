<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;
use DB;

class Product extends Model
{
    use HasFactory,Filterable;
    
    protected $fillable = ['name', 'price', 'reference', 'reference_number', 'purchase_price', 'barcode', 'image', 'description', 'private_comments', 'created_from', 'purchase_margin', 'sales_margin', 'discount', 'minimum_price', 'tax', 'images'];

    protected static $globalTable = 'products';

    public function getTable() {
        return self::$globalTable ;
    }

    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
    protected $appends = ['stock', 'virtual_stock', 'minimum_stock', 'amount', 'sales_stock_value', 'purchase_stock_value','product_category_name','price'];

    public function getAmountAttribute(){
        if(isset($this->attributes['price'])){

            $basePrice = $this->attributes['price'];
            $discount = isset($this->attributes['discount'])
            ? $this->attributes['discount'] : 0;
            $amount = ($basePrice - ($basePrice * $discount / 100)) ;
            return $amount;
            if(request()->client_id){
                $table = $this->getTable();
                $company_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
                $discount = get_product_special_price($company_id,request()->client_id,$this->attributes['id']);
                if($discount){
                    $discountAmount = ($discount / 100) * $basePrice;
                    return $basePrice - $discountAmount;
                }
            }elseif(request()->supplier_id){
                $table = $this->getTable();
                $company_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
                $specialPrice = get_product_supplier_special_price($company_id,request()->supplier_id,$this->attributes['id']);
                // if($specialPrice){
                    // $discountAmount = $basePrice - $specialPrice;
                    return $specialPrice;
                // }
            }
        }
        return $basePrice ;
    }

    public function product_attachments(){
        return $this->hasMany(ProductAttachment::class, 'product_id')->where('type' , attachment);
    }
    public function items(){
        return $this->hasMany(Item::class, 'reference_id')->where('reference' , 'pro');
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
        if(isset($this->attributes['id'])){
            $table = $this->getTable();
            $company_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            $product_stock = get_product_stock($company_id, $this->attributes['id']);
            if ($product_stock != NULL) {
                return $product_stock->sum('stock');
            }
            return '';
        }
        
    }
    public function getProductCategoryNameAttribute(){
        if(isset( $this->attributes['product_category_id'] )){
            $table = $this->getTable();
            $company_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_product_category_name($company_id, $this->attributes['product_category_id']);
        }
    }

    public function getVirtualStockAttribute()
    {
        if(isset($this->attributes['id'])){
            $product_stock =  ProductStock::where('product_id', $this->attributes['id'])->sum('stock');
            $items = $this->items()->sum('quantity');
            return $product_stock - $items;
        }
    }

    public function getMinimumStockAttribute()
    {
         if(isset($this->attributes['id'])){
            $table = $this->getTable();
            $company_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            $product_stock = get_product_stock($company_id, $this->attributes['id']);
            if ($product_stock != NULL) {
                return $product_stock->sum('minimum_stock');
            }
            return '';
        }
    }
    public function getPriceAttribute(){
        if(isset($this->attributes['price'])){

            $basePrice = $this->attributes['price'];
            if(request()->client_id){
                $table = $this->getTable();
                $company_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
                $discount = get_product_special_price($company_id,request()->client_id,$this->attributes['id']);
                if($discount){
                    $discountAmount = ($discount / 100) * $basePrice;
                    return $basePrice - $discountAmount;
                }
            }elseif(request()->supplier_id){
                $table = $this->getTable();
                $company_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
                $specialPrice = get_product_supplier_special_price($company_id,request()->supplier_id,$this->attributes['id']);
                // if($specialPrice){
                    // $discountAmount = $basePrice - $specialPrice;
                    return $specialPrice;
                // }
            }
            
            
        }
        return $basePrice ;
    }

    public function getSalesStockValueAttribute(){
        if(isset($this->stock) && isset($this->amount) ) {
            return (float)$this->stock*(float)$this->amount;
        }
    }

    public function getPurchaseStockValueAttribute(){

        if($this->stock && isset($this->attributes['purchase_price'])){
            return (float)$this->stock*(float)$this->attributes['purchase_price'];
        }
    }
    public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
    public function modelFilter()
    {
        return $this->provideFilter(\App\ModelFilters\ProductFilter::class);
    }
}