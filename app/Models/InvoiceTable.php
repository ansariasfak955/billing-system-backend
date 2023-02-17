<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;

use Awobaz\Compoships\Compoships;

class InvoiceTable extends Model
{
    use HasFactory, Compoships,Filterable;
    protected $guarded = ['id' , 'created_at', 'updated_at'];
    protected static $globalTable = 'invoice_tables' ;

    protected $appends = ['client_name', 'created_by_name', 'amount', 'meta_discount','amount_paid', 'amount_due', 'reference_type', 'payment_term_name', 'agent_name','amount_with_out_vat','tax_amount'];

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
    public function client(){

        return $this->hasOne(Client::class,'id', 'client_id');
    }
    public function clientAsset(){

        return $this->hasOne(ClientAsset::class,'id', 'asset_id');
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
    public function receipts(){

        return $this->hasMany(InvoiceReceipt::class, 'invoice_id');
    }

    public function getMetaDiscountAttribute(){
		if(isset($this->item_meta)){
			return $this->item_meta->pluck('discount')->first();
		}
    }

	public function getClientNameAttribute(){
        
        if(isset( $this->attributes['client_id'] )){
            $table = $this->getTable();
            $client_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_client_name($client_id, $this->attributes['client_id']);
        }
    }
    public function getPaymentTermNameAttribute(){
        
        if(isset( $this->attributes['payment_term'] )){
            $table = $this->getTable();
            $client_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_payment_terms_name($client_id, $this->attributes['payment_term']);
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
    public function getAmountWithOutVatAttribute(){
        if(isset($this->items)){
          return number_format($this->items->sum('amount_with_out_vat'),2);
        }
      }
      public function getTaxAmountAttribute(){
        if(isset($this->items)){
          return number_format($this->items->sum('taxAmount'),2);
        }
      }
	public function getAmountDueAttribute(){
        if(isset($this->attributes['status'])){
            if($this->attributes['status'] == 'paid'){
                return 0;
            }
        }
      if(isset($this->receipts)){
		$amount =  $this->receipts->where('paid', '0')->sum('amount');
        if($amount){
            return round($amount, 2);
        }
        return 0 ; 
	  }

    }
	public function getAmountPaidAttribute(){
      if(isset($this->receipts)){
		$amount =  $this->receipts->where('paid', '1')->sum('amount');
        if($amount){
            return round($amount, 2);
        }
        return 0 ; 
	  }
    }
    public function getDateAttribute(){

        if( isset( $this->attributes['date'] ) ){
            return date( 'Y-m-d', strtotime($this->attributes['date']) );
        }
    }
    public function getSentDateAttribute(){

        if( isset( $this->attributes['sent_date'] ) ){
            return date( 'Y-m-d', strtotime($this->attributes['sent_date']) );
        }
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
        return $this->provideFilter(\App\ModelFilters\InvoiceTableFilter::class);
    }
}
