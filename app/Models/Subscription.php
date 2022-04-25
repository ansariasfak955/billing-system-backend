<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'description', 'type'];

    protected static $globalTable = 'subscriptions';

    public function getTable() {
        return self::$globalTable;
    }

    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
}