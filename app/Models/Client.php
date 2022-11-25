<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $guarded = [
    	'id', 
		'created_at',
		'updated_at'
    ];

    public $appends = ['client_category_name'];

    protected static $globalTable = 'clients' ;

    public function getTable() {
        return self::$globalTable ;
    }

    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }

    public function client_attachments(){
        return $this->hasMany(ClientAttachment::class, 'client_id');
    }

    public function getClientCategoryNameAttribute(){
        
        if(isset( $this->attributes['client_category'] )){
            $table = $this->getTable();
            $company_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_category_name($company_id, $this->attributes['client_category']);
        }
    }
    public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
}
