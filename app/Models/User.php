<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Carbon\Carbon;

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
    protected $guarded = [
      'id', 'created_at', 'updated_at'
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

    protected $appends = ['company_country','default_country','company_id','enable_technical_module','logo', 'is_subscription_active', 'membership_name','subscription_amount', 'plan_expiry_days'];

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
    public function getIsSubscriptionActiveAttribute(){
    
        if(  isset($this->attributes['plan_expiry_date'])  ){
            if(!$this->attributes['stripe_subscription_id']){

                if( date( 'Y-m-d H:i:s', strtotime( $this->attributes['plan_expiry_date'] ) ) > date('Y-m-d H:i:s')  ){
                    return 1;
                }
            }else{
                if( date( 'Y-m-d H:i:s', strtotime( $this->attributes['plan_expiry_date'].'+10 days' ) ) > date('Y-m-d H:i:s')  ){
                    return 1;
                }
            }
        }
        return 0;
    }
    public function getPlanExpiryDateAttribute(){
        if( isset( $this->attributes['plan_expiry_date'] )  ){
            return date('d F Y' , strtotime($this->attributes['plan_expiry_date']));
        }
    }
    public function getMembershipNameAttribute(){
        if(isset($this->attributes['stripe_price_id'])){
            return Subscription::where('stripe_price_id', $this->attributes['stripe_price_id'])->pluck('name')->first();
        }
        
    }
    public function getSubscriptionAmountAttribute(){
        if(isset($this->attributes['stripe_price_id'])){
            return Subscription::where('stripe_price_id', $this->attributes['stripe_price_id'])->pluck('price')->first();
        }
        
    }
    public function getPlanExpiryDaysAttribute(){

        if(isset($this->attributes['plan_expiry_date'])){
            $currentDate = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
            $date = date('Y-m-d', strtotime($this->attributes['plan_expiry_date']));
            $expiryDate = Carbon::createFromFormat('Y-m-d', $date);
            $dayDifference = $expiryDate->diffInDays($currentDate);
            return $dayDifference;
        }
        
    }
}