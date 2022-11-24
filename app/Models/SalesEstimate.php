<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Awobaz\Compoships\Compoships;
use URL;

class SalesEstimate extends Model
{
    use HasFactory, Compoships;

    protected $fillable = [
    	'reference',
    	'reference_number',
		'date',
		'client_id',
		'status',
		'payment_option',
		'created_by',
		'title',
		'agent_id',
		'rate',
		'subject_to_vat',
		'subject_to_income_tax',
		'inv_address',
		'delivery_address',
		'email_sent_date',
		'valid_until',
		'currency',
		'currency_rate',
		'comments',
		'private_comments',
		'addendum',
		'name',
		'tin',
		'signature',
		'sent_date',
		'scheduled_delivery_date',
		'delivery_option'
    ];


    protected static $globalTable = 'sales_estimates' ;

    public function getTable() {
        return self::$globalTable ;
    }
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }

	protected $appends = ['client_name', 'created_by_name', 'amount', 'meta_discount'];

    public function getSignatureAttribute()
    {
    	if ($this->attributes['signature'] != NULL) {
    		return URL::to('/').'/storage/sales/signature/'.$this->attributes['signature'];
    	}
    	return '';
    }
    public function items(){
        return $this->hasMany(Item::class,['parent_id', 'type'], ['id', 'reference']);
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
	public function getDateAttribute(){

        if( isset( $this->attributes['date'] ) ){
            return date( 'Y-m-d', strtotime($this->attributes['date']) );
        }
    }
}
