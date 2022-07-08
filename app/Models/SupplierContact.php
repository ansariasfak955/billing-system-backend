<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierContact extends Model
{
    use HasFactory;
    protected $fillable = [
        "name",
        "phone",
        "email",
        "comments",
        "created_from",
        "supplier_id",
        "fax",
        "position"
    ];

    protected static $globalTable = 'supplier_contacts' ;

    public function getTable() {
        return self::$globalTable ;
    }
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
}
