<?php

namespace App\Models;

use Spatie\Permission\Models\Role as Model;
// use Spatie\Permission\Models\Permission as Model;

class Role extends Model
{
    protected static $globalTable = 'roles' ;

    public function __construct(array $attributes = array()) 
    {
        parent::__construct($attributes);
        if (request()->company_id != '') {
            self::$globalTable = 'company_'.request()->company_id.'_roles';
        }
    }

    protected $guard_name = 'api';

    public function getTable() {
        return self::$globalTable ;
    }
    
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
}