<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = ['format', 'account', 'bic_swift', 'name', 'description'];

    public function getIsDefaultAttribute()
    {
    	if (isset($this->attributes['is_default'])) {
    		return explode(',', $this->attributes['is_default']);
    	}

    	$arr = array("0");
    	return $arr;
    }
}
