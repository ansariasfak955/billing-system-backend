<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Company;


class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->integer('plan_id');
            $table->integer('user_id');
            $table->dateTime('expiry_date');
            $table->string('type');
            $table->float('amount', 8,2);
            $table->timestamps();
        });
        foreach(Company::pluck('id') as $company_id){
            
            if (!Schema::hasTable('company_'.$company_id.'_invoices')) {
                Schema::create('company_'.$company_id.'_invoices', function (Blueprint $table) {
                    //add same column here too
                    $table->id();
                    $table->integer('plan_id');
                    $table->integer('user_id');
                    $table->dateTime('expiry_date');
                    $table->string('type');
                    $table->float('amount', 8,2);
                    $table->timestamps();
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
        Schema::dropIfExists('invoices');
    }
}
