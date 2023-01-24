<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherConfiguration extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
        'value',
        'parent_id'
    ];

    protected static $globalTable = 'other_configurations' ;
    public function getTable() {
        return self::$globalTable ;
    }
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
