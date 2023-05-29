<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Awobaz\Compoships\Compoships;
use EloquentFilter\Filterable;

class Item extends Model
{
    use HasFactory, Compoships,Filterable;
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
    public $appends = ['amount', 'taxAmount','otherTaxAmount','amount_with_out_vat','product_name','product_reference_number'];

    public function invoice(){

        return $this->hasMany(InvoiceTable::class, 'parent_id');
    }
    public function getProductNameAttribute(){
        if(isset( $this->attributes['reference_id'] )){
            $table = $this->getTable();
            $company_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_product_name($company_id, $this->attributes['reference_id']);
        }
    }
    public function getProductReferenceNumberAttribute(){
        if(isset( $this->attributes['reference_id'] )){
            $table = $this->getTable();
            $company_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_product_reference_number($company_id, $this->attributes['reference_id']);
        }
    }

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
                return sprintf("%.2f",$taxAmount+$incTaxAmount);
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
                return sprintf("%.2f",$taxAmount+$incTaxAmount);
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
                return sprintf("%.2f",$amount+$taxAmount+$incTaxAmount);
            }
            return 0;
        }
    }
    public function getAmountWithOutVatAttribute(){
        if(isset($this->attributes['base_price'])){
            if(isset($this->attributes['quantity'])){
                $quantity = $this->attributes['quantity'];
            }else{
                $quantity = 1;
            }

            $basePrice = (float)$this->attributes['base_price'];
            if($basePrice){

                $amount = $basePrice * $quantity;
                return sprintf("%.2f",$amount);
            }
            return 0;
        }
    }
    public function modelFilter()
    {
        return $this->provideFilter(\App\ModelFilters\ItemFilter::class);
    }
    public function product(){
        return $this->hasOne(Product::class,['id', 'reference'], ['reference_id', 'reference']);
    }
}
