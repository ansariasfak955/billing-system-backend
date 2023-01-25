<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Company;

class AddColumnsInConsumptionTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consumption_taxes', function (Blueprint $table) {
            $table->enum('by_default', ['0','1'])->default('0')->nullable();
            $table->string('tax')->nullable();
        });
         foreach(Company::pluck('id') as $company_id){
            
            if (Schema::hasTable('company_'.$company_id.'_consumption_taxes')) {
                Schema::table('company_'.$company_id.'_consumption_taxes', function (Blueprint $table) {
                    $table->enum('by_default', ['0','1'])->default('0')->nullable();
                    $table->string('tax')->nullable();
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
        Schema::table('consumption_taxes', function (Blueprint $table) {
            //
        });
    }
}
