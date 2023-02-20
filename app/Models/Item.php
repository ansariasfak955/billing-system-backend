<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Awobaz\Compoships\Compoships;

class Item extends Model
{
    use HasFactory, Compoships;
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
        'vat',
        'reference_id',
    ];

    protected static $globalTable = 'items' ;

    public function getTable() {
        return self::$globalTable;
    }
    
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
    public $appends = ['amount', 'taxAmount','otherTaxAmount','amount_with_out_vat'];
    public function getTaxAmountAttribute(){
        if(isset($this->attributes['base_price'])){
            $tax = 0;
            $incTax = 0;
            if(isset($this->attributes['tax'])){
                $incTax = (int)$this->attributes['tax'];
            }
            if(isset($this->attributes['vat'])){
                $tax = (int)$this->attributes['vat'];
            }
            if(isset($this->attributes['quantity'])){
                $quantity = $this->attributes['quantity'];
            }else{
                $quantity = 1;
            }

            $basePrice = (float)$this->attributes['base_price'];
            $discount = (float)$this->attributes['discount'];
            if($basePrice){

                $amount = ($basePrice - ($basePrice * $discount / 100)) * $quantity;
                $taxAmount = 0;
                $incTaxAmount = 0;
                if($tax){
                    $taxAmount = ($tax / 100) * $amount;
                }
                // if($incTax){
                //     $incTaxAmount = ($incTax / 100) * $amount;
                // }
                return number_format((float)$taxAmount+$incTaxAmount, 2);
            }
            return 0;
        }
    }
    public function getOtherTaxAmountAttribute(){
        if(isset($this->attributes['base_price'])){
            // $tax = 0;
            $incTax = 0;
            if(isset($this->attributes['tax'])){
                $incTax = (int)$this->attributes['tax'];
            }
            // if(isset($this->attributes['vat'])){
            //     $tax = (int)$this->attributes['vat'];
            // }
            if(isset($this->attributes['quantity'])){
                $quantity = $this->attributes['quantity'];
            }else{
                $quantity = 1;
            }

            $basePrice = (float)$this->attributes['base_price'];
            $discount = (float)$this->attributes['discount'];
            if($basePrice){

                $amount = ($basePrice - ($basePrice * $discount / 100)) * $quantity;
                $taxAmount = 0;
                $incTaxAmount = 0;
                // if($tax){
                //     $taxAmount = ($tax / 100) * $amount;
                // }
                if($incTax){
                    $incTaxAmount = ($incTax / 100) * $amount;
                }
                return number_format((float)$taxAmount+$incTaxAmount, 2);
            }
            return 0;
        }
    }
    public function getAmountAttribute(){
        if(isset($this->attributes['base_price'])){
            $tax = 0;
            $incTax = 0;
            if(isset($this->attributes['tax'])){
                $incTax = (int)$this->attributes['tax'];
            }
            if(isset($this->attributes['vat'])){
                $tax = (int)$this->attributes['vat'];
            }
            if(isset($this->attributes['quantity'])){
                $quantity = $this->attributes['quantity'];
            }else{
                $quantity = 1;
            }

            $basePrice = (float)$this->attributes['base_price'];
            $discount = (float)$this->attributes['discount'];
            if($basePrice){

                $amount = ($basePrice - ($basePrice * $discount / 100)) * $quantity;
                $taxAmount = 0;
                $incTaxAmount = 0;
                if($tax){
                    $taxAmount = ($tax / 100) * $amount;
                }
                if($incTax){
                    $incTaxAmount = ($incTax / 100) * $amount;
                }
                return number_format((float)$amount+$taxAmount+$incTaxAmount, 2);
            }
            return 0;
        }
    }
    public function getAmountWithOutVatAttribute(){
        if(isset($this->attributes['base_price'])){
            if(isset($this->attributes['tax'])){
                $incTax = (int)$this->attributes['tax'];
            }
            if(isset($this->attributes['quantity'])){
                $quantity = $this->attributes['quantity'];
            }else{
                $quantity = 1;
            }

            $basePrice = (float)$this->attributes['base_price'];
            $discount = (float)$this->attributes['discount'];
            if($basePrice){

                $amount = ($basePrice - ($basePrice * $discount / 100)) * $quantity;
                return number_format((float)$amount, 2);
            }
            return 0;
        }
      }
}
