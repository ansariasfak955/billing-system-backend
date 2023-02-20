<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;

class TechnicalIncident extends Model
{
    use HasFactory,Filterable;

    protected $fillable = [
    	'reference',
    	'reference_number',
		'notifications',
		'client_id',
		'address',
		'priority',
		'status',
		'status_changed_by',
		'description',
		'created_by',
		'assigned_to',
		'date',
		'assigned_date',
		'invoice_to',
		'closing_date',
		'asset_id',
    ];

    protected static $globalTable = 'technical_incidents';
	public $appends = ['client_name','created_by_name','reference_type','assigned_to_name','invoice_to_name','client_legal_name'];
    public function getTable() {
        return self::$globalTable;
    }

    public function client(){

        return $this->hasOne(Client::class,'id', 'client_id');
    }


    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }

	public function getClientNameAttribute(){
        
        if(isset( $this->attributes['client_id'] )){
            $table = $this->getTable();
            $client_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_client_name($client_id, $this->attributes['client_id']);
        }
    }
    public function getInvoiceToNameAttribute(){
        if(isset( $this->attributes['invoice_to'] )){
            $table = $this->getTable();
            $client_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_client_name($client_id, $this->attributes['invoice_to']);
        }
    }
    public function getClientLegalNameAttribute(){
        
        if(isset( $this->attributes['client_id'] )){
            $table = $this->getTable();
            $client_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_client_legal_name($client_id, $this->attributes['client_id']);
        }
    }
    public function getDateAttribute(){

        if( isset( $this->attributes['date'] ) ){
            return date( 'Y-m-d', strtotime($this->attributes['date']) );
        }
    }
	public function getCreatedByNameAttribute(){
        
        if(isset( $this->attributes['created_by'] )){
            $table = $this->getTable();
            $createdby = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_client_name($createdby, $this->attributes['created_by']);
        }
    }
    public function getAssignedToNameAttribute(){

        if(isset( $this->attributes['assigned_to'] )){
            $table = $this->getTable();
            $createdby = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_user_name($createdby, $this->attributes['assigned_to']);
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
        return $this->provideFilter(\App\ModelFilters\TechnicalIncidentFilter::class);
    }
}
