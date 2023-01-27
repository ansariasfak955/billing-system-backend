<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Awobaz\Compoships\Compoships;
use EloquentFilter\Filterable;

class TechnicalTable extends Model
{
    use HasFactory, Compoships, Filterable;
    protected $guarded = ['id' , 'created_at', 'updated_at'];
    protected static $globalTable = 'technical_tables' ;

    public $appends = ['client_name','asset_name','payment_option_name','created_by_name', 'amount', 'meta_discount', 'reference_type', 'agent_name','amount_with_out_vat'];

    public function getTable() {
        return self::$globalTable ;
    }

    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }

    public function items(){
        return $this->hasMany(Item::class,['parent_id', 'type'], ['id', 'reference']);
    }
    public function clientAsset(){

        return $this->hasOne(ClientAsset::class,'id', 'asset_id');
    }
    
    public function item_meta(){
        return $this->hasMany(ItemMeta::class, 'parent_id');
    }
    public function client(){
        return $this->hasOne(Client::class, 'id', 'client_id');
    }
    public function payment_options(){
        return $this->hasOne(PaymentOption::class,'id', 'payment_option');
    }
    public function delivery_options(){
        return $this->hasOne(DeliveryOption::class,'id', 'delivery_option');
    }

    public function getClientNameAttribute(){
        
        if(isset( $this->attributes['client_id'] )){
            $table = $this->getTable();
            $client_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_client_name($client_id, $this->attributes['client_id']);
        }
    }

    public function getAssetNameAttribute(){
        
        if(isset( $this->attributes['asset_id'] )){
            $table = $this->getTable();
            $asset_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_asset_name($asset_id, $this->attributes['asset_id']);
        }
    }

    public function getPaymentOptionNameAttribute(){
        
        if(isset( $this->attributes['asset_id'] )){
            $table = $this->getTable();
            $asset_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_payment_option_name($asset_id, $this->attributes['asset_id']);
        }
    }

    public function getValidUntilAttribute(){
        
        if(isset( $this->attributes['valid_until'] )){
           return date( "d-m-Y", strtotime( $this->attributes['valid_until'] ) ); 
        }
    }
    public function getMetaDiscountAttribute(){
		if(isset($this->item_meta)){
			return $this->item_meta->pluck('discount')->first();
		}
    }
    public function getAmountWithOutVatAttribute(){
        if(isset($this->items)){
          return $this->items->sum('base_price');
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
		return $this->items->sum('amount');
	  }
    }
    public function getDateAttribute(){

        if( isset( $this->attributes['date'] ) ){
            return date( 'Y-m-d', strtotime($this->attributes['date']) );
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
        return $this->provideFilter(\App\ModelFilters\TechnicalTableFilter::class);
    }

}
