<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceReceipt extends Model
{
    use HasFactory;
    protected $guarded = ['id' , 'created_at', 'updated_at'];
    protected static $globalTable = 'invoice_receipts' ;

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

    public function invoice(){

        return $this->hasOne(InvoiceTable::class, 'id', 'invoice_id');
    }
}
