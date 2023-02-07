<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierBankAccount extends Model
{
    use HasFactory;
    protected $fillable = [
        'supplier_id',
        'format',
        'bank_account',
        'bic_swift',
        'bank_account_name',
        'is_default',
        'description'
    ];

    protected static $globalTable = 'supplier_bank_accounts' ;
    public function getTable() {
        return self::$globalTable ;
    }
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
}
