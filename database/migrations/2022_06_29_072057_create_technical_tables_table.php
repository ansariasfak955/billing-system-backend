<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTechnicalTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('technical_tables', function (Blueprint $table) {
                $table->id();
                $table->string('reference')->nullable();
                $table->string('reference_number')->nullable();
                $table->string('date')->nullable();
                $table->integer('client_id')->nullable();
                $table->string('status')->nullable();
                $table->string('payment_option')->nullable();
                $table->integer('created_by')->nullable();
                $table->string('title')->nullable();
                $table->string('agent_id')->nullable();
                $table->string('rate')->nullable();
                $table->integer('subject_to_vat')->default(0);
                $table->integer('subject_to_income_tax')->default(0);

                /* More Information Fields*/
                $table->string('asset_id')->nullable();
                $table->string('assigned_to')->nullable();
                $table->dateTime('assigned_date')->nullable();
                $table->dateTime('start_date')->nullable();
                $table->dateTime('end_date')->nullable();
                $table->dateTime('due_date')->nullable();
                $table->string('delivery_option')->nullable();
                $table->string('inv_address')->nullable();
                $table->string('work_address')->nullable();
                $table->string('email_sent_date')->nullable();
                $table->dateTime('valid_until')->nullable();
                $table->string('invoice_to')->nullable();
                $table->string('currency')->nullable();
                $table->float('currency_rate')->nullable();
                $table->string('comments')->nullable();
                $table->string('private_comments')->nullable();
                $table->string('addendum')->nullable();
                $table->string('create_from')->nullable();

                /* Signature Fields*/
                $table->string('name')->nullable();
                $table->string('tin')->nullable();
                $table->string('signature')->nullable();
                
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
        Schema::dropIfExists('technical_tables');
    }
}
