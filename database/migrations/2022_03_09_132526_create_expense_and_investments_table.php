<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpenseAndInvestmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expense_and_investments', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->nullable();
            $table->string('name');
            $table->float('price');
            $table->float('purchase_price')->nullable();
            $table->string('image')->nullable();
            $table->string('vat')->nullable();
            $table->enum('is_active', ['0', '1'])->default('1');
            $table->enum('type', ['expense', 'investment'])->default('expense');
            $table->integer('category_id')->default(0);
            $table->longText('description')->nullable();
            $table->longText('private_comments')->nullable();
            $table->string('created_from')->nullable();
            $table->enum('active_margin', ['0', '1'])->default('0');
            $table->float('purchase_margin')->nullable();
            $table->float('sales_margin')->nullable();
            $table->float('discount')->nullable();
            $table->float('minimum_price')->nullable();
            $table->string('tax')->nullable();
            $table->enum('is_promotional', ['0', '1'])->default('0');
            $table->enum('manage_stock', ['0', '1'])->default('0');
            $table->text('images')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expense_and_investments');
    }
}
