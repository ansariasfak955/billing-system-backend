<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'document_type', 'font', 'color'];

    protected static $globalTable = 'my_templates' ;

    public function getTable() {
        return self::$globalTable ;
    }

    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }

    protected $appends = ['template_preview'];

    public function getWatermarkAttribute()
    {
        if ($this->attributes['watermark']) {
            return url('/storage').'/templates/watermark/'.$this->attributes['watermark'];
        } else {
            return 'https://via.placeholder.com/400/fef4d0/060062&text=Not%20Found';
        }
    }

    public function metas()
    {
        return $this->hasMany(MyTemplateMeta::class, 'template_id', 'id');
    }

    public function getTemplatePreviewAttribute()
    {
        $table = $this->getTable();
        $company_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
        return 'https://billing.testingcafe.website/'.$company_id.'/template-preview/';
    }
}