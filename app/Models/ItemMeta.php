<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemMeta extends Model
{
    use HasFactory;

    protected $fillable = [
    	'reference_id',
		'discount',
		'income_tax'
    ];

    protected static $globalTable = 'item_metas' ;

    public function getTable() {
        return self::$globalTable ;
    }
    
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
}
