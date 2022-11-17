<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'commercial_name', 'country', 'state', 'city', 'address', 'pincode', 'phone', 'activity_type', 'email', 'legal_registration', 'corporate_color', 'register_as', 'tin', 'fax', 'number_of_employees', 'website', 'language', 'time_zone', 'fiscal_start_date', 'fiscal_start_month', 'number_of_decimal', 'decimal_separator', 'pdf_file_download_date_format', 'currency', 'currency_representation', 'taxpayer_identification', 'logo', 'user_id', 'enable_technical_module'];


    protected static function boot() {
        parent::boot();
        if(\Auth::check()){
            if (!\Auth::user()->hasRole(['admin']) ) {
                static::addGlobalScope('where', function (Builder $builder) {
                    $builder->where('user_id', \Auth::id());
                });
            }
        }
    }

    public function getLogoAttribute()
    {
        if ($this->attributes['logo']) {
            return url('/storage').'/company/logo/'.$this->attributes['logo'];
        } else {
            return 'https://via.placeholder.com/400/fef4d0/060062&text=Not%20Found';
        }
    }
}
