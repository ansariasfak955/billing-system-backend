<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Company;

class AddAmountColumnInInvoiceReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_receipts', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->default(0)->change();
        });
         foreach(Company::pluck('id') as $company_id){
            
            if (Schema::hasTable('company_'.$company_id.'_invoice_receipts')) {
                Schema::table('company_'.$company_id.'_invoice_receipts', function (Blueprint $table) {
                    //add same column here too
                    $table->decimal('amount', 10, 2)->default(0)->change();
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
        Schema::table('invoice_receipts', function (Blueprint $table) {
            //
        });
    }
}
