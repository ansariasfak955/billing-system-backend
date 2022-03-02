<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomState extends Model
{
    use HasFactory;

    protected $fillable = ['type_id', 'name', 'description', 'color'];

    protected static $globalTable = 'custom_states' ;

    public function getTable() {
        return self::$globalTable ;
    }
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
}
