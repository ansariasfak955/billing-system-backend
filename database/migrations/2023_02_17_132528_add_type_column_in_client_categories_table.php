<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Company;

class AddTypeColumnInClientCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('client_categories', function (Blueprint $table) {
            $table->string('type')->nullable();
        });
         foreach(Company::pluck('id') as $company_id){
            
            if (Schema::hasTable('company_'.$company_id.'_client_categories')) {
                Schema::table('company_'.$company_id.'_client_categories', function (Blueprint $table) {
                    //add same column here too
                    $table->string('type')->nullable();
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
        Schema::table('client_categories', function (Blueprint $table) {
            //
        });
    }
}
