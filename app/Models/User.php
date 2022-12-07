<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Auth;


class User extends Authenticatable
{
    use Notifiable, HasRoles, HasApiTokens;

    // protected $guard_name = 'api';
    
    protected static $globalTable = 'users' ;
    
    public function __construct(array $attributes = array()) 
    {
        parent::__construct($attributes);
        
        if (request()->company_id != '') {
            self::$globalTable = 'company_'.request()->company_id.'_users';
        }
    }

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


    public function getTable() {
        return self::$globalTable ;
    }

    public static function setGlobalTable($table) {
        self::$globalTable = $table;
    }

    protected $appends = ['company_country','default_country','company_id','enable_technical_module','logo'];

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

    public function getDefaultCountryAttribute()
    {
        $table = $this->getTable();
        $company_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
        $company = Company::where('user_id', Auth::id())->pluck('country')->first();

        if (isset($company_id)) {
            return get_company_country_name($company_id);
        }

        if ($company != NULL) {
            return $company->name;
        }

        return '';
    }

    public function getCompanyCountryAttribute()
    {
        return Company::where('user_id', Auth::id())->pluck('country')->first();
    }

    public function getCompanyIdAttribute()
    {
        $company_name = str_replace(' ', '', request()->company_name);
        $company = Company::where('name', request()->company_name)->orWhere('name', $company_name)->first();
        if ($company != NULL) {
            return $company->id;
        }
        return '';
    }

    public function getRoleAttribute()
    {
        $table = $this->getTable();
        $company_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
        $model_has_role = "company_".$company_id."_model_has_roles";

        if (isset(request()->user)) {
            $user_id = request()->user;
        } else {
            $user_id = Auth::id();
        }

        return \DB::table($model_has_role)->where('model_id', $user_id)->pluck('role_id')->first();
    }
    public function getEnableTechnicalModuleAttribute(){
        $table = $this->getTable();
        $company_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
        if(isset($company_id)){
            return Company::where('id', $company_id)->pluck('enable_technical_module')->first();
        }
    }
    public function getLogoAttribute(){
        $table = $this->getTable();
        $company_id = filter_var($table, FILTER_SANITIZE_NUMBER_INT);
        if(isset($company_id)){
            return Company::where('id', $company_id)->pluck('logo')->first();
        }
    }
}