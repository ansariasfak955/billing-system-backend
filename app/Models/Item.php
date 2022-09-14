<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    public $appends = ['amount'];

    public function getAmountAttribute(){
        if(isset($this->attributes['base_price'])){

            if(isset($this->attributes['tax'])){
                $tax = 0;
            }
            if(isset($this->attributes['quantity'])){
                $quantity = $this->attributes['quantity'];
            }else{
                $quantity = 1;
            }

            $basePrice = $this->attributes['base_price'];
            $discount = $this->attributes['discount'];
            $amount = ($basePrice - ($basePrice * $discount / 100)) * $quantity + $tax;
            return $amount;
        }
    }

    protected $fillable = [
    	'reference',
        'parent_id',
		'name',
		'description',
		'base_price',
		'quantity',
		'discount',
		'tax',
		'income_tax',
        'type',
        'subtotal',
        'vat'
    ];

    protected static $globalTable = 'items' ;

    public function getTable() {
        return self::$globalTable;
    }
    
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
}
