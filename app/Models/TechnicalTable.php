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

    public $appends = ['generated_id', 'client_name','asset_name','payment_option_name','created_by_name', 'amount','meta_discount', 'reference_type', 'agent_name','amount_with_out_vat','assign_to_name','tax_amount','client_legal_name'];

    public function getTable() {
        return self::$globalTable ;
    }

    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }

    public function items(){
        return $this->hasMany(Item::class,['parent_id', 'type'], ['id', 'reference']);
    }
    public function products(){
        $referenceType = Reference::where('type', 'Product')->pluck('prefix')->toArray();
        return $this->hasMany(Item::class,['parent_id', 'type'], ['id', 'reference'])->whereIn('reference',$referenceType);
    }
    public function services(){
        $referenceType = Reference::where('type', 'Service')->pluck('prefix')->toArray();
        return $this->hasMany(Item::class,['parent_id', 'type'], ['id', 'reference'])->whereIn('reference',$referenceType);
    }
    public function clientAsset(){

        return $this->hasOne(ClientAsset::class,'id', 'asset_id');
    }
    
    public function item_meta(){
        return $this->hasMany(ItemMeta::class, 'parent_id');
    }
    public function assign(){
        return $this->hasOne(User::class, 'id', 'assigned_to');
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
    public function getClientLegalNameAttribute(){
        
        if(isset( $this->attributes['client_id'] )){
            $table = $this->getTable();
            $client_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_client_legal_name($client_id, $this->attributes['client_id']);
        }
    }

    public function getAssetNameAttribute(){
        
        if(isset( $this->attributes['asset_id'] )){
            $table = $this->getTable();
            $asset_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_asset_name($asset_id, $this->attributes['asset_id']);
        }
    }

    public function getGeneratedFromAttribute() {
        $generated_from = $this->attributes['generated_from'];
        // return $generated_from;
        if($this->attributes['reference'] == 'WE' || $this->attributes['reference'] == 'WO' || $this->attributes['reference'] == 'WDN') {
            
            if(strpos($generated_from, 'INC') !== false){
                $generated_from = preg_replace('/INC\d+/', '', $generated_from);
            }

            if(strpos($generated_from, 'WE') !== false){
                $generated_from = preg_replace('/WE\d+/', '', $generated_from);
            }
            
            if(strpos($generated_from, 'WO') !== false){
                $generated_from = preg_replace('/WO\d+/', '', $generated_from);
            }

        }

        $generated_from = rtrim($generated_from);
        return str_replace(":", " ", $generated_from);
    }

    public function getGeneratedIdAttribute() {

        $generated_from = $this->attributes['generated_from'];
        if($this->attributes['reference'] == 'WE' || $this->attributes['reference'] == 'WO' || $this->attributes['reference'] == 'WDN'){
            //if string contains WE
            if(strpos($generated_from, 'WE') !== false){
                preg_match('/WE\d+/', $generated_from, $matches);
            }

            //if string contains WO
            if(strpos($generated_from, 'WO') !== false){
                preg_match('/WO\d+/', $generated_from, $matches);
            }

            //if string contains INC
            if(strpos($generated_from, 'INC') !== false){
                preg_match('/INC\d+/', $generated_from, $matches);
            }
        }
        if(isset($matches[0])){
            return $matches[0];
        }
        return '';

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
          return $this->items->sum('amount_with_out_vat');
        }
      }
      public function getTaxAmountAttribute(){
        if(isset($this->items)){
          return $this->items->sum('taxAmount');
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
    public function getAssignToNameAttribute(){
        
        if(isset( $this->attributes['assigned_to'] )){
            $table = $this->getTable();
            $company = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_user_email($company, $this->attributes['assigned_to']);
        }
    }
	public function getAmountAttribute(){
        
      if(isset($this->items)){
		$amount =  $this->items->sum('amount') ?? 0;
        return sprintf("%.2f",$amount);
	  }
    }
    public function getDateAttribute(){

        if( isset( $this->attributes['date'] ) ){
            return date( 'd/m/Y', strtotime($this->attributes['date']) );
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
