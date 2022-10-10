<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseTicket extends Model
{
    use HasFactory;
    
    protected $guarded = ['id' , 'created_at', 'updated_at'];

    protected static $globalTable = 'purchase_tickets' ;

    public $appends = ['paid_by_name', 'employee_name','supplier_name'];

    public function getTable() {
        return self::$globalTable ;
    }
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }

    public function getSupplierNameAttribute(){
        
        if(isset( $this->attributes['supplier_id'] )){
            $table = $this->getTable();
            $company = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_supplier_name($company, $this->attributes['supplier_id']);
        }
    }
    public function getPaidByNameAttribute(){
        
        if(isset( $this->attributes['paid_by'] )){
            $table = $this->getTable();
            $createdby = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_user_name($createdby, $this->attributes['paid_by']);
        }
    }
    public function getEmployeeNameAttribute(){
        
        if(isset( $this->attributes['employee'] )){
            $table = $this->getTable();
            $createdby = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_user_name($createdby, $this->attributes['employee']);
        }
    }
    public function getDateAttribute(){

        if( isset( $this->attributes['date'] ) ){
            return date( 'Y-m-d', strtotime($this->attributes['date']) );
        }
    }
}
