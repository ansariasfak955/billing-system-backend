<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;

class PurchaseReceipt extends Model
{
    use HasFactory,Filterable;
    
    protected $guarded = ['id' , 'created_at', 'updated_at'];

    protected static $globalTable = 'purchase_receipts' ;

    public function getTable() {
        return self::$globalTable ;
    }
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
    protected $appends = ['payment_option_name','paid_by_name'];

    public function invoice(){
        return $this->hasOne(PurchaseTable::class, 'id', 'purchase_id');
    }
    public function items(){

        return $this->hasMany(Item::class, 'parent_id');
    }
    public function supplier(){
        return $this->hasOne(Supplier::class, 'id', 'purchase_id');
    }
    public function client(){
        return $this->hasOne(Supplier::class, 'id', 'purchase_id');
    }
    public function getPaymentOptionNameAttribute(){
        if(isset($this->attributes['payment_option'])){
            $table = $this->getTable();
            $company_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_payment_option_name($company_id, $this->attributes['payment_option']);
        }
    }
    public function getPaidByNameAttribute(){
        
        if(isset( $this->attributes['paid_by'] )){
            $table = $this->getTable();
            $createdby = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_user_name($createdby, $this->attributes['paid_by']);
        }
    }
    public function getExpirationDateAttribute(){
        if(isset($this->attributes['expiration_date'])){
            return date('Y-m-d', strtotime($this->attributes['expiration_date']));
        }
    }
    public function getPaymentDateAttribute(){
        if(isset($this->attributes['payment_date'])){
            return date('Y-m-d', strtotime($this->attributes['payment_date']));
        }
    }
    public function modelFilter()
    {
        return $this->provideFilter(\App\ModelFilters\PurchaseReceiptFilter::class);
    }
}
