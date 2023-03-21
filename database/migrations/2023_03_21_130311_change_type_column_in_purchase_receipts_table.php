<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Company;

class ChangeTypeColumnInPurchaseReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_receipts', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_receipts', 'amount')){
                $table->decimal('amount', 10, 2)->default(0)->change();
            } 
        });
         foreach(Company::pluck('id') as $company_id){
            
            if (Schema::hasTable('company_'.$company_id.'_purchase_receipts')  && Schema::hasColumn('company_'.$company_id.'_purchase_receipts', 'amount')) {
                Schema::table('company_'.$company_id.'_purchase_receipts', function (Blueprint $table) use ($company_id){
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
        Schema::table('purchase_receipts', function (Blueprint $table) {
            //
        });
    }
}
