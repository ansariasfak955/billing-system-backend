<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseTicket extends Model
{
    use HasFactory;
    
    protected $guarded = ['id' , 'created_at', 'updated_at'];

    protected static $globalTable = 'purchase_tickets' ;

    public function getTable() {
        return self::$globalTable ;
    }
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
}
