<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;


class User extends Authenticatable
{
    use Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'mobile_number', 'username', 'surname', 'role', 'tin', 'country', 'phone_1', 'phone_2', 'position', 'calendar', 'address', 'city', 'state', 'pincode', 'country', 'language', 'gmail_sender_name', '    gmail_email_address', 'smtp_sender_name', 'smtp_email_address', 'smtp_server', 'smtp_security_protocol', 'smtp_password', 'smtp_port'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected static $globalTable = 'users' ;

    public function getTable() {
        return self::$globalTable ;
    }
    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }

    public function companies()
    {
        return $this->hasMany(Company::class, 'user_id', 'id');
    }

    public function getImageAttribute()
    {
        if ($this->attributes['image']) {
            return url('/storage').'/users/'.$this->attributes['image'];
        } else {
            return 'https://via.placeholder.com/400/fef4d0/060062&text=Not%20Found';
        }
    }
}
