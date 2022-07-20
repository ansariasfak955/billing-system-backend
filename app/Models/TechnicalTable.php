<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicalTable extends Model
{
    use HasFactory;
    protected $guarded = ['id' , 'created_at', 'updated_at'];
    protected static $globalTable = 'technical_tables' ;

    public $appends = ['client_name','asset_name','payment_option_name'];

    public function getTable() {
        return self::$globalTable ;
    }

    public static function setGlobalTable($table) {
        self::$globalTable = $table;
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

}
