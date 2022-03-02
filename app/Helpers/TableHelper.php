<?php
namespace App\Helpers;

use App\Models\Company;
use App\Models\CustomStateType;
use App\Models\CustomState;

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
                $table->enum('is_default', ['0', '1'])->default('0');
                $table->longText('description')->nullable();
                $table->timestamps();
            });
        }

        /* Creating dynamic company based products table */
        if (!Schema::hasTable('company_'.$company_id.'_products')) {
            Schema::create('company_'.$company_id.'_products', function (Blueprint $table) {
                $table->increments('id');
                $table->string('reference')->nullable();
                $table->string('name');
                $table->float('price');
                $table->float('purchase_price')->nullable();
                $table->string('barcode')->nullable();
                $table->string('image')->nullable();
                $table->integer('product_category_id')->default(0);
                $table->enum('is_active', ['0', '1'])->default('1');
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

        /* Creating dynamic company based custom states table */
        if (!Schema::hasTable('company_'.$company_id.'_custom_states')) {
            Schema::create('company_'.$company_id.'_custom_states', function (Blueprint $table) {
                $table->id();
                $table->integer('type_id');
                $table->string('name')->nullable();
                $table->text('description')->nullable();
                $table->string('color')->nullable();
                $table->timestamps();
            });
        }
        /* Creating dynamic company based custom state type  table */
        if (!Schema::hasTable('company_'.$company_id.'_custom_state_types')) {
            Schema::create('company_'.$company_id.'_custom_state_types', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->timestamps();
            });

            $types = ['Incident', 'Purchase Delivery Note', 'Purchase Order', 'Sales Delivery Note', 'Sales Estimate', 'Sales Order', 'Work Delivery Note', 'Work Estimate', 'Work Order'];
            foreach($types as $type){
                $custom_state_type =  new CustomStateType;
                CustomStateType::setGlobalTable('company_'.$company_id.'_custom_state_types') ;
                $custom_state =  new CustomState;
                CustomState::setGlobalTable('company_'.$company_id.'_custom_states') ;
                $custom_type = $custom_state_type->setTable('company_'.$company_id.'_custom_state_types')->create(["name" => $type]);
                $custom_state->setTable('company_'.$company_id.'_custom_states')->create([
                    "name" => "Pending",
                    "description" => "",
                    "color" => "#ffe66e",
                    "type_id" => $custom_type->id
                ]);
                $custom_state->setTable('company_'.$company_id.'_custom_states')->create([
                    "name" => "Refused",
                    "description" => "",
                    "color" => "#ff7272",
                    "type_id" => $custom_type->id
                ]);
                $custom_state->setTable('company_'.$company_id.'_custom_states')->create([
                    "name" => "In Progress",
                    "description" => "",
                    "color" => "#FD9A64",
                    "type_id" => $custom_type->id
                ]);
                $custom_state->setTable('company_'.$company_id.'_custom_states')->create([
                    "name" => "Closed",
                    "description" => "",
                    "color" => "#49ce31",
                    "type_id" => $custom_type->id
                ]);
            }
        }

    }
}