<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Awobaz\Compoships\Compoships;
use EloquentFilter\Filterable;

class PurchaseTable extends Model
{
    use HasFactory, Compoships,Filterable;
    protected $guarded = ['id' , 'created_at', 'updated_at'];
    protected static $globalTable = 'purchase_tables' ;

    protected $appends = ['client_name', 'created_by_name', 'amount', 'meta_discount', 'supplier_name', 'agent_name','sub_total', 'vat', 'amount_vat', 'percentage','income_tax', 'amount_income_tax','reference_type', 'payment_term_name','amount_with_out_vat','total_quantity'];

    public function getTable() {
        return self::$globalTable ;
    }
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
    public function items(){
        return $this->hasMany(Item::class,['parent_id', 'type'], ['id', 'reference']);
    }
    public function item_meta(){

        return $this->hasMany(ItemMeta::class, 'parent_id');
    }
    public function receipts(){

        return $this->hasMany(PurchaseReceipt::class, 'purchase_id');
    }
    public function supplier(){

        return $this->hasOne(Supplier::class,'id', 'supplier_id');
    }
    // public function client(){

    //     return $this->hasOne(Supplier::class,'id', 'supplier_id');
    // }
    public function category(){
        return $this->hasOne(ClientCategory::class,'id', 'client_category');
    }
    public function payment_options(){
        return $this->hasOne(PaymentOption::class,'id', 'payment_option');
    }
    public function payment_terms(){
        return $this->hasOne(PaymentTerm::class,'id', 'payment_term');
    }
    public function delivery_options(){
        return $this->hasOne(DeliveryOption::class,'id', 'delivery_option');
    }
    public function getMetaDiscountAttribute(){
		if(isset($this->item_meta)){
			return $this->item_meta->pluck('discount')->first();
		}
    }

	public function getClientNameAttribute(){
        
        if(isset( $this->attributes['supplier_id'] )){
            $table = $this->getTable();
            $company = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_supplier_name($company, $this->attributes['supplier_id']);
        }
    }
    public function getAmountWithOutVatAttribute(){
        if(isset($this->items)){
          return number_format($this->items->sum('amount_with_out_vat'),2);
        }
      }
      public function getTotalQuantityAttribute(){
        if(isset($this->items)){
          return $this->items->sum('quantity');
        }
      }
	public function getPaymentTermNameAttribute(){
        
        if(isset( $this->attributes['payment_term'] )){
            $table = $this->getTable();
            $client_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_payment_terms_name($client_id, $this->attributes['payment_term']);
        }
    }
    
	public function getSupplierNameAttribute(){
        
        if(isset( $this->attributes['supplier_id'] )){
            $table = $this->getTable();
            $company = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_supplier_name($company, $this->attributes['supplier_id']);
        }
    }

	public function getCreatedByNameAttribute(){
        
        if(isset( $this->attributes['created_by'] )){
            $table = $this->getTable();
            $createdby = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_user_name($createdby, $this->attributes['created_by']);
        }
    }
    public function getAgentNameAttribute(){
        
        if(isset( $this->attributes['agent_id'] )){
            $table = $this->getTable();
            $company = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_user_name($company, $this->attributes['agent_id']);
        }
    }
	public function getAmountAttribute(){
      if(isset($this->items)){
		return number_format($this->items->sum('amount'),2);
	  }
    }
    
    public function getCreatedAtAttribute(){

        if( isset( $this->attributes['created_at'] ) ){
            return date( 'Y-m-d', strtotime($this->attributes['created_at']) );
        }
    }
    public function getDateAttribute(){

        if( isset( $this->attributes['date'] ) ){
            return date( 'Y-m-d', strtotime($this->attributes['date']) );
        }
    }
    public function getEmailSentDateAttribute(){

        if( isset( $this->attributes['email_sent_date'] ) ){
            return date('Y-m-d', strtotime($this->attributes['email_sent_date']));
        }
    }
    public function getSentDateAttribute(){

        if( isset( $this->attributes['sent_date'] ) ){
            return date('Y-m-d', strtotime($this->attributes['sent_date']));
        }
    }
    public function getSubTotalAttribute(){

        if(isset($this->items)){
            return $this->items->sum('base_price');
        }
    }
    public function getVatAttribute(){

        if(isset($this->items)){
            $vat =  $this->items->sum('vat');
            return $vat;          
        }
    }
    public function getAmountVatAttribute(){

        if(isset($this->items)){
            $total =  $this->items->sum('base_price');
            $vat =  $this->items->sum('vat');
            if($total && $vat){
                return $total-($vat/100*$total);
            }
        }
    }
    public function getReferenceTypeAttribute(){
        if(isset( $this->attributes['reference'] )){
            $table = $this->getTable();
            $company_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_reference_type($company_id, $this->attributes['reference']);
        }
    }
    public function getPercentageAttribute(){

        return 0;
    }
    public function getIncomeTaxAttribute(){

        return 0;
    }
    public function getAmountIncomeTaxAttribute(){

        return number_format(0,2);
    }
    public function modelFilter()
    {
        return $this->provideFilter(\App\ModelFilters\PurchaseTableFilter::class);
    }
}
