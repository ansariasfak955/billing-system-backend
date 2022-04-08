<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        "client_id",
        "address",
        "state",
        "city",
        "zip_code",
        "country",
        "address_latitude",
        "address_longitude",
        "type",
        "extra_information",
        "description"
    ];

    protected static $globalTable = 'client_addresses' ;

    public function getTable() {
        return self::$globalTable ;
    }
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
}
