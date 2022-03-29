<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as Model;

class Permission extends Model
{
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->with('children');
    }


    public function parent()
    {
        return $this->hasMany(self::class, 'id', 'parent_id');
    }
}