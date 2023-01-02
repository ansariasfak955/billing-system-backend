<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Company;

class AddTestColumnInProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->default(0)->change();
            $table->decimal('purchase_price', 10, 2)->default(0)->change();
            $table->decimal('purchase_margin', 10, 2)->default(0)->change();
            $table->decimal('sales_margin', 10, 2)->default(0)->change();
            $table->decimal('discount', 10, 2)->default(0)->change();
            $table->decimal('minimum_price', 10, 2)->default(0)->change();
        });
         foreach(Company::pluck('id') as $company_id){
            
            if (Schema::hasTable('company_'.$company_id.'_products')) {
                Schema::table('company_'.$company_id.'_products', function (Blueprint $table) {
                    $table->decimal('price', 10, 2)->default(0)->change();
                    $table->decimal('purchase_price', 10, 2)->default(0)->change();
                    $table->decimal('purchase_margin', 10, 2)->default(0)->change();
                    $table->decimal('sales_margin', 10, 2)->default(0)->change();
                    $table->decimal('discount', 10, 2)->default(0)->change();
                    $table->decimal('minimum_price', 10, 2)->default(0)->change();
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
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
}
