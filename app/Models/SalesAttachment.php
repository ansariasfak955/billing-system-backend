<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        "sales_id",
        "document",
        "description",
    ];

    protected static $globalTable = 'sales_attachments' ;

    public function getTable() {
        return self::$globalTable ;
    }
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }

    public function getDocumentAttribute()
    {
        if ($this->attributes['document']) {
            return url('/storage').'/sales/documents/'.$this->attributes['document'];
        } else {
            return 'https://via.placeholder.com/400/fef4d0/060062&text=Not%20Found';
        }
    }
}