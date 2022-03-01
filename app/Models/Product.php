<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'reference', 'purchase_price', 'barcode', 'image', 'description', 'private_comments', 'created_from', 'purchase_margin', 'sales_margin', ' discount', 'minimum_price', 'tax', 'images'];

    public function getImageAttribute()
    {
        if ($this->attributes['image']) {
            return url('/storage').'/products/images/'.$this->attributes['image'];
        } else {
            return 'https://via.placeholder.com/400/fef4d0/060062&text=Not%20Found';
        }
    }
}
