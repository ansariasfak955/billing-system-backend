<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomeTax extends Model
{
    use HasFactory;
    protected $guarded = ['id', 'created_at', 'updated_at'];
    
    protected static $globalTable = 'income_taxes';

    public function getTable() {
        return self::$globalTable ;
    }

    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
    public function getTaxesAttribute(){
        if(isset($this->attributes['taxes'])){
            return json_decode($this->attributes['taxes']);
        }
    }
}
