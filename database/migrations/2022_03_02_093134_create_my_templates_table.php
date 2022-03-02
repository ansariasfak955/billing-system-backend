<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMyTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('my_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('document_type')->nullable();
            $table->string('font')->nullable();
            $table->string('watermark')->nullable();
            $table->string('color')->nullable();
            $table->enum('is_default', ['0', '1'])->default('0');
            $table->enum('hide_company_information', ['0', '1'])->default('0');
            $table->enum('hide_assets_information', ['0', '1'])->default('0');
            $table->enum('show_signature_box', ['0', '1'])->default('0');
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
        Schema::dropIfExists('my_templates');
    }
}
