<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
    	"reference",
        "reference_number",
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
        "agent",
    	"currency",
    	"subject_to_vat",
    	"maximum_risk",
    	"bank_account_format",
    	"bank_account_account",
    	"bank_account_bic",
    	"bank_account_name",
    	"bank_account_description",
        "client_category",
        "swift_aba",
        "ced_ruc",
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
}
