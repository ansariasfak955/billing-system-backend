<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientContact extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
		"name",
		"phone",
		"email",
		"comments",
		"created_from",
		"client_id",
		"fax",
		"position"
    ];

    protected static $globalTable = 'clients' ;

    public function client(){

        return $this->hasOne(Client::class,'id', 'client_id');
    }

    public function getTable() {
        return self::$globalTable ;
    }
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
}
