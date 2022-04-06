<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        "client_id",
        "address",
        "name",
        "identifier",
        "serial_number",
        "brand",
        "description",
        "private_comments",
        "model",
        "subject_to_maintenance",
        "start_of_warranty",
        "end_of_warranty",
        "main_image",
    ];

    protected static $globalTable = 'client_assets' ;

    public function getTable() {
        return self::$globalTable ;
    }
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
}
