<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Company;


class CreateSupplierBankAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_id')->nullable();
            $table->string('format')->default('other');
            $table->string('bank_account');
            $table->string('bic_swift')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('is_default')->default('0');
            $table->longText('description')->nullable();
            $table->timestamps();
        });
        foreach(Company::pluck('id') as $company_id){
            
            if (!Schema::hasTable('company_'.$company_id.'_supplier_bank_accounts')) {
                Schema::create('company_'.$company_id.'_supplier_bank_accounts', function (Blueprint $table) {
                    //add same column here too
                    $table->id();
                    $table->string('supplier_id')->nullable();
                    $table->string('format')->default('other');
                    $table->string('bank_account');
                    $table->string('bic_swift')->nullable();
                    $table->string('bank_account_name')->nullable();
                    $table->string('is_default')->default('0');
                    $table->longText('description')->nullable();
                    $table->timestamps();
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
        Schema::dropIfExists('supplier_bank_accounts');
    }
}
