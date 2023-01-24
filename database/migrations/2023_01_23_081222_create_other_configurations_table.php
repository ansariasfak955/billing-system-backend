<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Company;


class CreateOtherConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('other_configurations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type')->nullable();
            $table->string('value')->nullable();
            $table->string('parent_id')->nullable();
            $table->timestamps();
        });
        foreach(Company::pluck('id') as $company_id){
            
            if (!Schema::hasTable('company_'.$company_id.'_other_configurations')) {
                Schema::create('company_'.$company_id.'_other_configurations', function (Blueprint $table) {
                    //add same column here too
                    $table->increments('id');
                    $table->string('type')->nullable();
                    $table->string('value')->nullable();
                    $table->string('parent_id')->nullable();
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
        Schema::dropIfExists('other_configurations');
    }
}
