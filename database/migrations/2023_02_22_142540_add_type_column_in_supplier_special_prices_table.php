<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Company;

class AddTypeColumnInSupplierSpecialPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_special_prices', function (Blueprint $table) {
            //
            $table->string('type',20)->nullable();
        });
         foreach(Company::pluck('id') as $company_id){
            
            if (Schema::hasTable('company_'.$company_id.'_supplier_special_prices')) {
                Schema::table('company_'.$company_id.'_supplier_special_prices', function (Blueprint $table) {
                    //add same column here too
                    $table->string('type',20)->nullable();
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
        Schema::table('supplier_special_prices', function (Blueprint $table) {
            //
        });
    }
}
