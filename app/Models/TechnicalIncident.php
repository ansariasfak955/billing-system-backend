<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicalIncident extends Model
{
    use HasFactory;

    protected $fillable = [
    	'reference',
    	'reference_number',
		'notifications',
		'client_id',
		'address',
		'priority',
		'status',
		'status_changed_by',
		'description',
		'created_by',
		'assigned_to',
		'date',
		'assigned_date',
		'invoice_to',
		'closing_date',
		'asset_id',
    ];

    protected static $globalTable = 'technical_incidents';

    public function getTable() {
        return self::$globalTable;
    }

    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }
}
