<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $appends = ['status'];
    
    protected static $globalTable = 'invoices';

    public function getTable() {
        return self::$globalTable ;
    }

    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
    public function getStatusAttribute(){

        if(isset($this->attributes['expiry_date'])){

            if( date( 'Y-m-d H:i:s', strtotime( $this->attributes['expiry_date'] ) ) > date('Y-m-d H:i:s')  ){
                return 'active';
            }
        }
        return 'expired';
    }
    public function getExpiryDateAttribute(){
        if( isset( $this->attributes['expiry_date'] )  ){
            return date('d F Y' , strtotime($this->attributes['expiry_date']));
        }
    }
}
