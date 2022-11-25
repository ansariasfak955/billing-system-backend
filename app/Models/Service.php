<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'reference', 'purchase_price', 'image', 'description', 'private_comments', 'vat', 'created_from', 'purchase_margin', 'sales_margin', 'discount', 'minimum_price', 'tax', 'images','reference_number'];

    protected static $globalTable = 'services' ;

    public function getTable() {
        return self::$globalTable ;
    }
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }

    public function getImageAttribute()
    {
        if ($this->attributes['image']) {
            return url('/storage').'/services/images/'.$this->attributes['image'];
        } else {
            return 'https://via.placeholder.com/400/fef4d0/060062&text=Not%20Found';
        }
    }
    public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }

}
