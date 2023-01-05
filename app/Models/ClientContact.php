<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;

class ClientContact extends Model
{
    use HasFactory,Filterable;

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

    protected $appends = ['client_name'];

    public function client(){

        return $this->hasOne(Client::class,'id', 'client_id');
    }

    public function getTable() {
        return self::$globalTable ;
    }
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
    public function modelFilter()
    {
        return $this->provideFilter(\App\ModelFilters\ClientContactFilter::class);
    }
    public function getClientNameAttribute(){
        
        if(isset( $this->attributes['client_id'] )){
            $table = $this->getTable();
            $client_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_client_name($client_id, $this->attributes['client_id']);
        }
    }
}
