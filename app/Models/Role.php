<?php

namespace App\Models;

use Spatie\Permission\Models\Role as Model;
// use Spatie\Permission\Models\Permission as Model;

class Role extends Model
{
    protected static $globalTable = 'roles' ;

    protected $guard_name = 'api';

    public function getTable() {
        return self::$globalTable ;
    }
    
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
}