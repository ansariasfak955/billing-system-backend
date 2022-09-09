<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use URL;

class SalesEstimate extends Model
{
    use HasFactory;

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
		'signature'
    ];


    protected static $globalTable = 'sales_estimates' ;

    public function getTable() {
        return self::$globalTable ;
    }
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }

	protected $appends = ['client_name', 'created_by_name', 'amount'];

    public function getSignatureAttribute()
    {
    	if ($this->attributes['signature'] != NULL) {
    		return URL::to('/').'/storage/sales/signature/'.$this->attributes['signature'];
    	}
    	return '';
    }
    public function items(){

        return $this->hasMany(Item::class, 'parent_id');
    }
    public function itemMeta(){

        return $this->hasMany(ItemMeta::class, 'parent_id');
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
}
