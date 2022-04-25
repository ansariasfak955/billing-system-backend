<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityType extends Model
{
    use HasFactory;

    protected $fillable = ['activity_type'];

    protected static $globalTable = 'activity_types';

    public function getTable() {
        return self::$globalTable;
    }

    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
}