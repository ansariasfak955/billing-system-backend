<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;

class Client extends Model
{
    use HasFactory,Filterable;

    protected $guarded = [
    	'id', 
		'created_at',
		'updated_at'
    ];

    public $appends = ['client_category_name','payment_terms_name','payment_option_name','reference_type'];

    protected static $globalTable = 'clients' ;

    public function getTable() {
        return self::$globalTable ;
    }

    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }

    public function client_attachments(){
        return $this->hasMany(ClientAttachment::class, 'client_id');
    }
    public function category(){
        return $this->hasOne(ClientCategory::class,'id', 'client_category');
    }
    public function payment_options(){
        return $this->hasOne(PaymentOption::class,'id', 'payment_option_id');
    }
    public function payment_terms(){
        return $this->hasOne(PaymentTerm::class,'id', 'payment_terms_id');
    }
    public function getClientCategoryNameAttribute(){
        
        if(isset( $this->attributes['client_category'] )){
            $table = $this->getTable();
            $company_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_category_name($company_id, $this->attributes['client_category']);
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
    public function getReferenceTypeAttribute(){
        if(isset( $this->attributes['reference'] )){
            $table = $this->getTable();
            $company_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_reference_type($company_id, $this->attributes['reference']);
        }
    }
    public function modelFilter()
    {
        return $this->provideFilter(\App\ModelFilters\ClientFilter::class);
    }
}
