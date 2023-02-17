<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;

class ClientCategory extends Model
{
    use HasFactory,Filterable;
    protected $guarded = [
        "id",
        "type",
        "created_at",
        "updated_at",
    ];

    protected static $globalTable = 'client_categories' ;

    public function getTable() {
        return self::$globalTable ;
    }
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
    public function modelFilter()
    {
        return $this->provideFilter(\App\ModelFilters\ClientCategoryFilter::class);
    }
}
