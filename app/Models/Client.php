<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
    	"reference",
    	"legal_name",
    	"tin",
    	"phone_1",
    	"address",
    	"state",
    	"country",
    	"name",
    	"email",
    	"city",
    	"zip_code",
    	"fax",
    	"website",
    	"comments",
    	"popup_notice",
    	"created_from",
    	"phone_2",
    	"payment_date",
    	"discount",
    	"rate",
    	"currency",
    	"subject_to_vat",
    	"maximum_risk",
    	"bank_account_format",
    	"bank_account_account",
    	"bank_account_bic",
    	"bank_account_name",
    	"bank_account_description"
    ];

    protected static $globalTable = 'clients' ;

    public function getTable() {
        return self::$globalTable ;
    }
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
}
