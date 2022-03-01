<?php
namespace App\Helpers;

use App\Models\Company;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableHelper
{
    public static function createTables($company_id)
    {
        /* Creating dynamic company based roles table */
        if (!Schema::hasTable('company_'.$company_id.'_roles')) {
            Schema::create('company_'.$company_id.'_roles', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->timestamps();
            });
        }

        /* Create dynamic permissions table */
        if (!Schema::hasTable('company_'.$company_id.'_permissions')) {
            Schema::create('company_'.$company_id.'_permissions', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('slug');
                $table->timestamps();
            });
        }

        /* Creating dynamic company based role permissions table */
        if (!Schema::hasTable('company_'.$company_id.'_role_has_permissions')) {
            Schema::create('company_'.$company_id.'_role_has_permissions', function (Blueprint $table) {
                $table->integer('permission_id');
                $table->integer('role_id');
            });
        }

        /* Creating dynamic company based users permissions table */
        if (!Schema::hasTable('company_'.$company_id.'_users_permissions')) {
            Schema::create('company_'.$company_id.'_users_permissions', function (Blueprint $table) {
                $table->integer('user_id');
                $table->integer('permission_id');
            });
        }

        /* Creating dynamic company based model has roles table */
        if (!Schema::hasTable('company_'.$company_id.'_model_has_roles')) {
            Schema::create('company_'.$company_id.'_model_has_roles', function (Blueprint $table) {
                $table->integer('role_id');
                $table->string('model_type');
                $table->integer('model_id');
            });
        }

        /* Creating dynamic company based model has permissions table */
        if (!Schema::hasTable('company_'.$company_id.'_model_has_permissions')) {
            Schema::create('company_'.$company_id.'_model_has_permissions', function (Blueprint $table) {
                $table->integer('permission_id');
                $table->string('model_type');
                $table->integer('model_id');
            });
        }

        /* Creating dynamic company based users table */
        if (!Schema::hasTable('company_'.$company_id.'_users')) {
            Schema::create('company_'.$company_id.'_users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->nullable();
                $table->string('surname')->nullable();
                $table->string('tin')->nullable();
                $table->string('email')->nullable();
                $table->string('photo')->nullable();
                $table->string('phone_1')->nullable();
                $table->string('phone_2')->nullable();
                $table->string('position')->nullable();
                $table->string('calendar')->nullable();
                $table->longText('address')->nullable();
                $table->string('city')->nullable();
                $table->string('state')->nullable();
                $table->string('pincode')->nullable();
                $table->string('country')->nullable();
                $table->string('language')->nullable();
                $table->timestamps();
            });
        }

        /* Creating dynamic company based role permissions table */
        if (!Schema::hasTable('company_'.$company_id.'_user_roles')) {
            Schema::create('company_'.$company_id.'_user_roles', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('company_user_id');
                $table->integer('company_role_id');
                $table->timestamps();
            });
        }

        /* Creating dynamic company based bank accounts table */
        if (!Schema::hasTable('company_'.$company_id.'_bank_accounts')) {
            Schema::create('company_'.$company_id.'_bank_accounts', function (Blueprint $table) {
                $table->increments('id');
                $table->string('format')->default('other');
                $table->string('account');
                $table->string('bic_swift')->nullable();
                $table->string('name')->nullable();
                $table->string('is_default')->default('0');
                $table->longText('description')->nullable();
                $table->timestamps();
            });
        }

        /* Creating dynamic company based products table */
        if (!Schema::hasTable('company_'.$company_id.'_products')) {
            Schema::create('company_'.$company_id.'_products', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->float('price');
                $table->string('barcode')->nullable();
                $table->string('image')->nullable();
                $table->integer('product_category_id')->default(0);
                $table->string('is_active')->default('1');
                $table->longText('description')->nullable();
                $table->longText('private_comments')->nullable();
                $table->string('created_from')->nullable();
                $table->timestamps();
            });
        }

        /* Creating dynamic company based products table */
        if (!Schema::hasTable('company_'.$company_id.'_product_categories')) {
            Schema::create('company_'.$company_id.'_product_categories', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->timestamps();
            });
        }

        /* Creating dynamic company based rate table */
        if (!Schema::hasTable('company_'.$company_id.'_rates')) {
            Schema::create('company_'.$company_id.'_rates', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->timestamps();
            });
        }
    }
}