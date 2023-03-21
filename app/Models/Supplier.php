<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;

class Supplier extends Model
{
    use HasFactory,Filterable;
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected static $globalTable = 'suppliers' ;

    public $appends = ['supplier_category_name','payment_terms_name','payment_option_name','reference_type'];

    public function getTable() {
        return self::$globalTable ;
    }
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
    public function payment_options(){
        return $this->hasOne(PaymentOption::class,'id', 'payment_option_id');
    }
    public function payment_terms(){
        return $this->hasOne(PaymentTerm::class,'id', 'payment_terms_id');
    }
    public function purchases(){
        return $this->hasMany(PurchaseTable::class, 'supplier_id');
    }
    public function getSupplierCategoryNameAttribute(){
        
        if(isset( $this->attributes['supplier_category'] )){
            $table = $this->getTable();
            $company_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_category_name($company_id, $this->attributes['supplier_category']);
        }
    }
    public function category(){
        return $this->hasOne(ClientCategory::class,'id', 'supplier_category');
    }
    public function getReferenceTypeAttribute(){
        if(isset( $this->attributes['reference'] )){
            $table = $this->getTable();
            $company_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_reference_type($company_id, $this->attributes['reference']);
        }
    }
    public function getPaymentTermsNameAttribute(){
        if(isset( $this->attributes['payment_terms_id'] )){
            $table = $this->getTable();
            $company_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_payment_terms_name($company_id, $this->attributes['payment_terms_id']);
        }
    }
    public function getPaymentOptionNameAttribute(){
        if(isset( $this->attributes['payment_option_id'] )){
            $table = $this->getTable();
            $company_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_payment_option_name($company_id, $this->attributes['payment_option_id']);
        }
    }

    public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
    public function modelFilter()
    {
        return $this->provideFilter(\App\ModelFilters\SupplierFilter::class);
    }
}
