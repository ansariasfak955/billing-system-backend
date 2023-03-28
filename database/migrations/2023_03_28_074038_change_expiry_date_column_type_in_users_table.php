<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Company;

class ChangeExpiryDateColumnTypeInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('stripe_customer_id')->nullable();
            $table->string('stripe_price_id')->nullable();
            $table->string('stripe_subscription_id')->nullable();
            $table->dateTime('plan_expiry_date')->nullable();
        });
        foreach(Company::pluck('id') as $company_id){
            if (Schema::hasTable('company_'.$company_id.'_users')  && Schema::hasColumn('company_'.$company_id.'_users', 'plan_expiry_date')) {
                Schema::table('company_'.$company_id.'_users', function (Blueprint $table) use ($company_id){
                    //add same column here too
                    $table->dateTime('plan_expiry_date')->nullable()->change();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
