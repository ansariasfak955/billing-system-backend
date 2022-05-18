<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as Model;

class Permission extends Model
{
	protected static $globalTable = 'permissions' ;

	public function getTable() {
        return self::$globalTable ;
    }
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->with('children');
    }


    public function parent()
    {
        return $this->hasMany(self::class, 'id', 'parent_id');
    }
}