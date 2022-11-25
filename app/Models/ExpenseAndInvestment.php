<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseAndInvestment extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'reference', 'purchase_price', 'image', 'description', 'private_comments', 'vat', 'created_from', 'purchase_margin', 'sales_margin', ' discount', 'minimum_price', 'tax', 'images','reference_number'];

    protected static $globalTable = 'expense_and_investments' ;

    public function getTable() {
        return self::$globalTable ;
    }
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }

    public function getImageAttribute()
    {
        if ($this->attributes['image']) {
            return url('/storage').'/expense_n_investments/images/'.$this->attributes['image'];
        } else {
            return 'https://via.placeholder.com/400/fef4d0/060062&text=Not%20Found';
        }
    }
}
