<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomStateType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    protected static $globalTable = 'custom_state_types' ;

    public function getTable() {
        return self::$globalTable ;
    }
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }

    public function states()
    {
        return $this->hasMany(CustomState::class, 'type_id', 'id');
    }
}
