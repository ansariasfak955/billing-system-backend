<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Company;

class AddColumnsInIncomeTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('income_taxes', function (Blueprint $table) {
            if(!Schema::hasColumn('income_taxes', 'by_default')){
                $table->enum('by_default', ['0','1'])->default('0')->nullable();
            }
            if(!Schema::hasColumn('income_taxes', 'tax')){
                $table->string('tax')->nullable();
            }
        });
         foreach(Company::pluck('id') as $company_id){
            
            if (Schema::hasTable('company_'.$company_id.'_income_taxes') && !Schema::hasColumn('company_'.$company_id.'_income_taxes','by_default')) {
                Schema::table('company_'.$company_id.'_income_taxes', function (Blueprint $table) {
                    $table->enum('by_default', ['0','1'])->default('0')->nullable();
                });
            }
            if (Schema::hasTable('company_'.$company_id.'_income_taxes') && !Schema::hasColumn('company_'.$company_id.'_income_taxes','tax')) {
                Schema::table('company_'.$company_id.'_income_taxes', function (Blueprint $table) {
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
        Schema::table('income_taxes', function (Blueprint $table) {
            //
        });
    }
}
