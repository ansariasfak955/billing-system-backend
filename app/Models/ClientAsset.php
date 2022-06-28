<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        "client_id",
        "reference",
        "reference_number",
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

    public function getMainImageAttribute()
    {
        if ($this->attributes['main_image']) {
            return url('/storage').'/clients/assets/'.$this->attributes['main_image'];
        } else {
            return 'https://via.placeholder.com/400/fef4d0/060062&text=Not%20Found';
        }
    }
}
