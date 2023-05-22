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
    public function productStock(){
        return $this->hasMany(ProductStock::class, 'product_id');
    }
    public function items(){
        return $this->hasMany(Item::class, 'reference_id')->where('reference' , 'pro');
    }
    public function purchase(){
        return $this->hasMany(Item::class, 'reference_id')->where('type','PINV');
    }
    public function invoice(){
        return $this->hasMany(Item::class, 'reference_id')->where('type','INV');
    }

    public function getVirtualStockAttribute(){
        $purchaseItems = $this->purchase()->sum('quantity');
        $productStock = $this->productStock()->sum('stock');
        $invoiceStock = $this->invoice()->sum('quantity');
        return  ($productStock - $purchaseItems) + $invoiceStock;
    }
}