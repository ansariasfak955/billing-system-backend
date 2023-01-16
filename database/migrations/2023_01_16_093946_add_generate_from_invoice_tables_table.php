<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Company;

class AddGenerateFromInvoiceTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_tables', function (Blueprint $table) {
            $table->string('generated_from')->nullable();
        });
         foreach(Company::pluck('id') as $company_id){
            
            if (Schema::hasTable('company_'.$company_id.'_invoice_tables')) {
                Schema::table('company_'.$company_id.'_invoice_tables', function (Blueprint $table) {
                    //add same column here too
                    $table->string('generated_from')->nullable();
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
        Schema::table('invoice_tables', function (Blueprint $table) {
            //
        });
    }
}
