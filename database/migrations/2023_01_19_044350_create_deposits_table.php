<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Company;


class CreateDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deposits', function (Blueprint $table) {
            $table->increments('id');
            $table->string('concept')->nullable();
            $table->string('date')->nullable();
            $table->string('payment_option')->nullable();
            $table->string('amount')->nullable();
            $table->string('paid_by')->nullable();
            $table->timestamps();
        });
        foreach(Company::pluck('id') as $company_id){
            
            if (!Schema::hasTable('company_'.$company_id.'_deposits')) {
                Schema::create('company_'.$company_id.'_deposits', function (Blueprint $table) {
                    //add same column here too
                    $table->increments('id');
                    $table->string('concept')->nullable();
                    $table->string('date')->nullable();
                    $table->string('payment_option')->nullable();
                    $table->string('amount')->nullable();
                    $table->string('paid_by')->nullable();
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
        Schema::dropIfExists('deposits');
    }
}
