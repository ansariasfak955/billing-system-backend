<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Awobaz\Compoships\Compoships;

class InvoiceTable extends Model
{
    use HasFactory, Compoships;
    protected $guarded = ['id' , 'created_at', 'updated_at'];
    protected static $globalTable = 'invoice_tables' ;

    protected $appends = ['client_name', 'created_by_name', 'amount', 'meta_discount','amount_paid', 'amount_due'];

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

	public function getCreatedByNameAttribute(){
        
        if(isset( $this->attributes['created_by'] )){
            $table = $this->getTable();
            $createdby = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_client_name($createdby, $this->attributes['created_by']);
        }
    }

	public function getAmountAttribute(){
      if(isset($this->items)){
		return $this->items->sum('amount');
	  }
    }
	public function getAmountDueAttribute(){

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
}
