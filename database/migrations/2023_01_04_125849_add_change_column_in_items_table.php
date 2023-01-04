<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Company;

class AddChangeColumnInItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'subtotal')){
                $table->decimal('subtotal', 10, 2)->default(0.00)->change();
            }   
        });
         foreach(Company::pluck('id') as $company_id){
            
            if (Schema::hasTable('company_'.$company_id.'_items') && Schema::hasColumn('company_'.$company_id.'_items', 'subtotal')) {
                Schema::table('company_'.$company_id.'_items', function (Blueprint $table) use ($company_id){
                        $table->decimal('subtotal', 10, 2)->default(0.00)->change();
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
