<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefaultPdfSendOption extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'format', 'price_after_tax', 'mailing_format', 'include_main_image'];

    protected static $globalTable = 'default_pdf_send_options' ;

    public function getTable() {
        return self::$globalTable ;
    }
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
}
