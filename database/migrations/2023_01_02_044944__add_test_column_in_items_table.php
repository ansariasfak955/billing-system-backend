<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Company;

class AddTestColumnInItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->decimal('base_price', 10, 2)->default(0)->change();
            $table->decimal('discount', 10, 2)->default(0)->change();
        });
         foreach(Company::pluck('id') as $company_id){
            
            if (Schema::hasTable('company_'.$company_id.'_items')) {
                Schema::table('company_'.$company_id.'_items', function (Blueprint $table) {
                    $table->decimal('base_price', 10, 2)->default(0)->change();
                    $table->decimal('discount', 10, 2)->default(0)->change();
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
        Schema::table('items', function (Blueprint $table) {
            //
        });
    }
}
