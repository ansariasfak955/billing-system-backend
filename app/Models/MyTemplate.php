<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'document_type', 'font', 'color', 'is_default', 'hide_company_information', 'hide_assets_information', 'show_signature_box'];

    protected static $globalTable = 'my_templates' ;

    public function getTable() {
        return self::$globalTable ;
    }
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
}
