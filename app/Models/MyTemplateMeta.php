<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyTemplateMeta extends Model
{
    use HasFactory;
    
    public $timestamps = false;

    protected $fillable = ['template_id', 'option_name', 'option_value', 'category', 'type'];

    protected static $globalTable = 'my_template_metas' ;

    public function getTable() {
        return self::$globalTable ;
    }
    
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }

    public function getOptionNameAttribute()
    {
        if($this->attributes['option_name']) {
            $arr = explode('_',$this->attributes['option_name']);
            $count = count($arr);
            if($count > 0 ){
                $key = $count-1;
                return $arr[$key];
            }
        }
    }
}