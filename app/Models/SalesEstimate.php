<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Awobaz\Compoships\Compoships;
use EloquentFilter\Filterable;
use URL;

class SalesEstimate extends Model
{
    use HasFactory, Compoships, Filterable;

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
		'delivery_option',
        'generated_from'
    ];


    protected static $globalTable = 'sales_estimates' ;

    public function getTable() {
        return self::$globalTable ;
    }
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
    
	public function client(){
        return $this->hasOne(Client::class,'id', 'client_id');
    }
    public function payment_options(){
        return $this->hasOne(PaymentOption::class,'id', 'payment_option');
    }
    public function delivery_options(){
        return $this->hasOne(DeliveryOption::class,'id', 'delivery_option');
    }
	protected $appends = ['generated_id', 'client_name', 'created_by_name', 'amount', 'meta_discount','reference_type','agent_name','amount_with_out_vat','tax_amount','client_legal_name'];

    public function getSignatureAttribute()
    {
    	if ($this->attributes['signature'] != NULL) {
    		return URL::to('/').'/storage/sales/signature/'.$this->attributes['signature'];
    	}
    	return '';
    }
    public function products(){
        $referenceType = Reference::where('type', 'Product')->pluck('prefix')->toArray();
        return $this->hasMany(Item::class,['parent_id', 'type'], ['id', 'reference'])->whereIn('reference',$referenceType);
    }
    public function services(){
        $referenceType = Reference::where('type', 'Service')->pluck('prefix')->toArray();
        return $this->hasMany(Item::class,['parent_id', 'type'], ['id', 'reference'])->whereIn('reference',$referenceType);
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
    public function getClientLegalNameAttribute(){
        
        if(isset( $this->attributes['client_id'] )){
            $table = $this->getTable();
            $client_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_client_legal_name($client_id, $this->attributes['client_id']);
        }
    }
    public function getAgentNameAttribute(){
        
        if(isset( $this->attributes['agent_id'] )){
            $table = $this->getTable();
            $company = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_user_name($company, $this->attributes['agent_id']);
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
    public function getAmountWithOutVatAttribute(){
        if(isset($this->items)){
            $amount =  $this->items->sum('amount_with_out_vat') ?? 0.00;
            return number_format($amount, 2, '.', '');	
        }
    }

    public function getGeneratedFromAttribute() {
        $generated_from = $this->attributes['generated_from'];
        if ($this->attributes['reference'] == 'SE' || $this->attributes['reference'] == 'SO' || $this->attributes['reference'] == 'SDN') {
            $generated_from = preg_replace('/SE\d+/', '', $generated_from);
            $generated_from = preg_replace('/SO\d+/', '', $generated_from);
        }

        $generated_from = rtrim($generated_from);
        return str_replace(":", " ", $generated_from);

    }

    public function getGeneratedIdAttribute() {

        $generated_from = $this->attributes['generated_from'];
        if($this->attributes['reference'] == 'SE' || $this->attributes['reference'] == 'SO' || $this->attributes['reference'] == 'SDN'){
            preg_match('/SE\d+/', $generated_from, $matches);
            preg_match('/SO\d+/', $generated_from, $matches);
        }
        if(isset($matches[0])){
            return $matches[0];
        }
        return '';

    }

	public function getAmountAttribute(){
        if(isset($this->items)){
            $amount =  $this->items->sum('amount') ?? 0.00;
            return number_format($amount, 2, '.', '');	  
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
        return $this->provideFilter(\App\ModelFilters\SalesTableFilter::class);
    }
}
