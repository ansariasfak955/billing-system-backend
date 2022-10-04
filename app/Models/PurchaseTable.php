<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseTable extends Model
{
    use HasFactory;
    protected $guarded = ['id' , 'created_at', 'updated_at'];
    protected static $globalTable = 'purchase_tables' ;

    protected $appends = ['client_name', 'created_by_name', 'amount', 'meta_discount', 'supplier_name', 'agent_name'];

    public function getTable() {
        return self::$globalTable ;
    }
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
    public function items(){

        return $this->hasMany(Item::class, 'parent_id');
    }
    public function item_meta(){

        return $this->hasMany(ItemMeta::class, 'parent_id');
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
		return $this->items->sum('amount');
	  }
    }
    
    public function getCreatedAtAttribute(){

        if( isset( $this->attributes['created_at'] ) ){
            return date( 'Y-m-d', strtotime($this->attributes['created_at']) );
        }
    }
}
