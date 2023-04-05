<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use EloquentFilter\Filterable;

class ExpenseAndInvestment extends Model
{
    use HasFactory,Filterable;

    protected $fillable = ['name', 'price', 'reference', 'purchase_price', 'image', 'description', 'private_comments', 'vat', 'created_from', 'purchase_margin', 'sales_margin', ' discount', 'minimum_price', 'tax', 'images','reference_number'];


    protected $appends = ['expense_category_name'];
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
    public function getExpenseCategoryNameAttribute(){
        if(isset( $this->attributes['category_id'] )){
            $table = $this->getTable();
            $company_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
            return get_expense_category_name($company_id, $this->attributes['category_id']);
        }
    }
    public function modelFilter()
    {
        return $this->provideFilter(\App\ModelFilters\ExpensiInvestmentFilter::class);
    }
}
