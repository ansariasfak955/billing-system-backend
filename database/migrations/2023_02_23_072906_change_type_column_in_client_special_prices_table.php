<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Company;

class ChangeTypeColumnInClientSpecialPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('client_special_prices', function (Blueprint $table) {
            $table->decimal('purchase_price', 10, 2)->default(0)->change();
            $table->decimal('sales_price', 10, 2)->default(0)->change();
            $table->decimal('discount', 10, 2)->default(0)->change();
            $table->decimal('special_price', 10, 2)->default(0)->change();
        });
         foreach(Company::pluck('id') as $company_id){
            
            if (Schema::hasTable('company_'.$company_id.'_client_special_prices')) {
                Schema::table('company_'.$company_id.'_client_special_prices', function (Blueprint $table) {
                    //add same column here too
                    $table->decimal('purchase_price', 10, 2)->default(0)->change();
                    $table->decimal('sales_price', 10, 2)->default(0)->change();
                    $table->decimal('discount', 10, 2)->default(0)->change();
                    $table->decimal('special_price', 10, 2)->default(0)->change();
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
        Schema::table('client_special_prices', function (Blueprint $table) {
            //
        });
    }
}
