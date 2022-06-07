<?php
namespace App\Helpers;

use App\Models\Company;
use App\Models\CustomStateType;
use App\Models\CustomState;
use App\Models\MyTemplate;
use App\Models\MyTemplateMeta;
use App\Models\DefaultPdfSendOption;
use App\Models\Setting;
use App\Models\PaymentOption;
// use Spatie\Permission\Models\Role;
use App\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\Api\UserController;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableHelper
{
    public static function createTables($company_id)
    {
        $roles = Role::get();

        /* set tables in config files */
        (new UserController())->setConfig($company_id);

        /* Creating dynamic company based roles table */
        if (!Schema::hasTable('company_'.$company_id.'_roles')) {
            Schema::create('company_'.$company_id.'_roles', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('guard_name')->nullable();
                $table->timestamps();
            });
        }

        if ($roles != NULL) {
            $roles_table = 'company_'.$company_id.'_roles';
            Role::setGlobalTable($roles_table);
            foreach ($roles as $role) {
                if (!Role::where('name', $role->name)->exists()) {
                    Role::create([
                        'name' => $role->name,
                        'guard_name' => 'api',
                    ]);
                }
            }
        }
        
        /* Creating dynamic clients table */
        if (!Schema::hasTable('company_'.$company_id.'_clients')) {
            Schema::create('company_'.$company_id.'_clients', function (Blueprint $table) {
                $table->id();
                $table->string('reference')->nullable();
                $table->string('reference_number')->nullable();
                $table->string('legal_name')->nullable();
                $table->string('tin')->nullable();
                $table->string('phone_1')->nullable();
                $table->string('address')->nullable();
                $table->string('state')->nullable();
                $table->string('country')->nullable();
                $table->string('name')->nullable();
                $table->string('email')->nullable();
                $table->string('city')->nullable();
                $table->string('zip_code')->nullable();
                $table->string('address_latitude')->nullable();
                $table->string('address_longitude')->nullable();

                /* general fields */
                $table->string('fax')->nullable();
                $table->string('website')->nullable();
                $table->string('comments')->nullable();
                $table->string('popup_notice')->nullable();
                $table->string('created_from')->default('web');
                $table->string('phone_2')->nullable();
                $table->integer('client_category')->default(0);

                /* commercial fields */
                $table->integer('payment_option_id')->default(0);
                $table->string('payment_date')->nullable();
                $table->float('discount')->nullable();
                $table->string('rate')->nullable();
                $table->string('currency')->nullable();
                $table->enum('subject_to_vat', ['0', '1'])->default(0);
                $table->float('maximum_risk')->nullable();
                $table->integer('payment_terms_id')->default(0);
                $table->string('payment_adjustment')->default("unspecified");
                $table->integer('agent')->default(0);
                $table->integer('invoice_to')->default(0);
                $table->enum('subject_to_income_tax', ['0', '1'])->default(0);

                /* bank account fields */
                $table->string('bank_account_format')->nullable();
                $table->string('bank_account_account')->nullable();
                $table->string('bank_account_bic')->nullable();
                $table->string('bank_account_name')->nullable();
                $table->string('bank_account_description')->nullable();
                $table->timestamps();
            });
        }

        /* Creating dynamic clients special prices table */
        if (!Schema::hasTable('company_'.$company_id.'_client_special_prices')) {
            Schema::create('company_'.$company_id.'_client_special_prices', function (Blueprint $table) {
                $table->id();
                $table->string('client_id');
                $table->string('product_id')->nullable();
                $table->float('purchase_price')->nullable();
                $table->float('sales_price')->nullable();
                $table->string('purchase_margin')->nullable();
                $table->string('sales_margin')->nullable();
                $table->float('discount')->nullable();
                $table->float('special_price')->nullable();
                $table->timestamps();
            });
        }

        /* Creating dynamic client contacts table */
        if (!Schema::hasTable('company_'.$company_id.'_client_contacts')) {
            Schema::create('company_'.$company_id.'_client_contacts', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->longText('comments')->nullable();
                $table->integer('client_id');
                $table->string('fax')->nullable();
                $table->string('position')->nullable();
                $table->timestamps();
            });
        }

        /* Creating dynamic client addresses table */
        if (!Schema::hasTable('company_'.$company_id.'_client_addresses')) {
            Schema::create('company_'.$company_id.'_client_addresses', function (Blueprint $table) {
                $table->id();
                $table->integer('client_id')->default(0);
                $table->string('address')->nullable();
                $table->string('state')->nullable();
                $table->string('city')->nullable();
                $table->string('zip_code')->nullable();
                $table->string('country')->nullable();
                $table->string('address_latitude')->nullable();
                $table->string('address_longitude')->nullable();
                $table->string('type')->default("other");
                $table->longText('extra_information')->nullable();
                $table->longText('description')->nullable();
                $table->timestamps();
            });
        }

        /* Creating dynamic sales estimates table */
        if (!Schema::hasTable('company_'.$company_id.'_sales_estimates')) {
            Schema::create('company_'.$company_id.'_sales_estimates', function (Blueprint $table) {
                $table->id();
                $table->string('reference')->nullable();
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
                $table->string('inv_address')->nullable();
                $table->string('delivery_address')->nullable();
                $table->string('email_sent_date')->nullable();
                $table->string('valid_until')->nullable();
                $table->string('currency')->nullable();
                $table->float('currency_rate')->nullable();
                $table->string('comments')->nullable();
                $table->string('private_comments')->nullable();
                $table->string('addendum')->nullable();

                /* Signature Fields*/
                $table->string('name')->nullable();
                $table->string('tin')->nullable();
                $table->string('signature')->nullable();
                
                $table->timestamps();
            });
        }

        /* Creating dynamic sales attachments table */
        if (!Schema::hasTable('company_'.$company_id.'_sales_attachments')) {
            Schema::create('company_'.$company_id.'_sales_attachments', function (Blueprint $table) {
                $table->id();
                $table->integer('sales_id');
                $table->string('document')->nullable();
                $table->longText('description')->nullable();
                $table->timestamps();
            });
        }

        /* Creating dynamic sales attachments table */
        if (!Schema::hasTable('company_'.$company_id.'_technical_incidents')) {
            Schema::create('company_'.$company_id.'_technical_incidents', function (Blueprint $table) {
                $table->id();
                $table->string('reference')->nullable();
                $table->string('notifications')->nullable();
                $table->string('client_id')->nullable();
                $table->string('address')->nullable();
                $table->string('priority')->nullable();
                $table->string('status')->nullable();
                $table->string('status_changed_by')->nullable();
                $table->longText('description')->nullable();
                $table->string('created_by')->nullable();
                $table->dateTime('date')->nullable();
                $table->dateTime('assigned_date')->nullable();
                $table->string('invoice_to')->nullable();
                $table->dateTime('closing_date')->nullable();
                $table->timestamps();
            });
        }

        /* Creating dynamic client assets table */
        if (!Schema::hasTable('company_'.$company_id.'_technical_incident_attachments')) {
            Schema::create('company_'.$company_id.'_technical_incident_attachments', function (Blueprint $table) {
                $table->id();
                $table->integer('technical_incident_id');
                $table->string('document')->nullable();
                $table->longText('description')->nullable();
                $table->timestamps();
            });
        }

        /* Creating dynamic items table */
        if (!Schema::hasTable('company_'.$company_id.'_items')) {
            Schema::create('company_'.$company_id.'_items', function (Blueprint $table) {
                $table->id();
                $table->string('reference');
                $table->string('reference_id');
                $table->string('name')->nullable();
                $table->longText('description')->nullable();
                $table->float('base_price')->nullable();
                $table->string('quantity')->nullable();
                $table->float('discount')->nullable();
                $table->string('tax')->nullable();
                $table->string('income_tax')->nullable();
                $table->timestamps();
            });
        }

        /* Creating dynamic item metas table */
        if (!Schema::hasTable('company_'.$company_id.'_item_metas')) {
            Schema::create('company_'.$company_id.'_item_metas', function (Blueprint $table) {
                $table->id();
                $table->integer('reference_id');
                $table->float('discount')->nullable();
                $table->integer('income_tax')->nullable();
                $table->timestamps();
            });
        }

        /* Creating dynamic client assets table */
        if (!Schema::hasTable('company_'.$company_id.'_client_assets')) {
            Schema::create('company_'.$company_id.'_client_assets', function (Blueprint $table) {
                $table->id();
                $table->string('client_id')->nullable();
                $table->string('address')->nullable();
                $table->string('name')->nullable();
                $table->string('identifier')->nullable();
                $table->string('serial_number')->nullable();
                $table->string('brand')->nullable();
                $table->longText('description')->nullable();
                $table->longText('private_comments')->nullable();
                $table->string('model')->nullable();
                $table->enum('subject_to_maintenance', ['0', '1'])->nullable();
                $table->date('start_of_warranty')->nullable();
                $table->date('end_of_warranty')->nullable();
                $table->string('main_image')->nullable();
                $table->timestamps();
            });
        }

        /* Creating dynamic client attachments table */
        if (!Schema::hasTable('company_'.$company_id.'_client_attachments')) {
            Schema::create('company_'.$company_id.'_client_attachments', function (Blueprint $table) {
                $table->id();
                $table->string('client_id')->nullable();
                $table->string('document')->nullable();
                $table->longText('description')->nullable();
                $table->timestamps();
            });
        }

        /* Creating dynamic subscriptions table */
        if (!Schema::hasTable('company_'.$company_id.'_subscriptions')) {
            Schema::create('company_'.$company_id.'_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->float('price')->nullable();
                $table->longText('description')->nullable();
                $table->timestamps();
            });
        }

        /* Creating dynamic payment options table */
        if (!Schema::hasTable('company_'.$company_id.'_payment_options')) {
            Schema::create('company_'.$company_id.'_payment_options', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->enum('by_default', ['0', '1'])->deafult('0');
                $table->enum('link_bank_account', ['0', '1'])->deafult('0');
                $table->longText('description')->nullable();
                $table->timestamps();

            });

            $payment_option =  new PaymentOption;
            PaymentOption::setGlobalTable('company_'.$company_id.'_payment_options') ;

            $payment_option->setTable('company_'.$company_id.'_payment_options')->create([
                "name" => "Bank Transfer",
                "by_default" => "0",
                "link_bank_account" => "1",
                "description" => "A series of instructions that are offered on behalf of the account holder to a financial entity so that they may withdraw the funds from our account and pay it into the account of another person or company."
            ]);

            $payment_option->setTable('company_'.$company_id.'_payment_options')->create([
                "name" => "Cash",
                "by_default" => "0",
                "link_bank_account" => "0",
                "description" => "A payment made in cash."
            ]);

            $payment_option->setTable('company_'.$company_id.'_payment_options')->create([
                "name" => "Check",
                "by_default" => "0",
                "link_bank_account" => "0",
                "description" => "A document that orders a bank to pay a specific amount of money from a person's account to the person in whose name the cheque has been issued."
            ]);

            $payment_option->setTable('company_'.$company_id.'_payment_options')->create([
                "name" => "Direct Debit",
                "by_default" => "0",
                "link_bank_account" => "0",
                "description" => "A document that indicates a financial transaction in which one person withdraws funds from another person's bank account."
                ]);
            
        }

        /* Creating dynamic client and supplier category table */
        if (!Schema::hasTable('company_'.$company_id.'_client_supplier_categories')) {
            Schema::create('company_'.$company_id.'_client_supplier_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('rate')->nullable();
                $table->longText('description')->nullable();
                $table->timestamps();
            });
        }

        /* Creating dynamic payment terms table */
        if (!Schema::hasTable('company_'.$company_id.'_payment_terms')) {
            Schema::create('company_'.$company_id.'_payment_terms', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->longText('description')->nullable();
                $table->timestamps();
            });
        }

        /* Create dynamic permissions table */
        if (!Schema::hasTable('company_'.$company_id.'_permissions')) {
            Schema::create('company_'.$company_id.'_permissions', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('guard_name')->nullable();
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
                $table->string('username')->nullable();
                $table->string('surname')->nullable();
                $table->string('role')->nullable();
                $table->string('tin')->nullable();
                $table->string('email')->nullable();
                $table->string('password')->nullable();
                $table->string('image')->nullable();
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
                $table->enum('has_access', ['0', '1'])->default('1');
                $table->enum('use_email_configuartion', ['gmail', 'smtp'])->default('gmail');
                $table->string('gmail_sender_name')->nullable();
                $table->string('gmail_email_address')->nullable();
                $table->string('smtp_sender_name')->nullable();
                $table->string('smtp_email_address')->nullable();
                $table->string('smtp_server')->nullable();
                $table->string('smtp_security_protocol')->nullable();
                $table->string('smtp_password')->nullable();
                $table->string('smtp_port')->nullable();
                $table->string('mobile_number')->nullable();
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
                $table->string('reference_number')->nullable();
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

        /* Creating dynamic product stocks table */
        if (!Schema::hasTable('company_'.$company_id.'_product_stocks')) {
            Schema::create('company_'.$company_id.'_product_stocks', function (Blueprint $table) {
                $table->id();
                $table->integer('product_id');
                $table->string('warehouse');
                $table->float('stock')->default(0);
                $table->float('virtual_stock')->default(0);
                $table->float('minimum_stock')->default(0);
                $table->string('location')->nullable();
                $table->timestamps();
            });
        }

        /* Creating dynamic company based services table */
        if (!Schema::hasTable('company_'.$company_id.'_services')) {
            Schema::create('company_'.$company_id.'_services', function (Blueprint $table) {
                $table->increments('id');
                $table->string('reference')->nullable();
                $table->string('name');
                $table->float('price');
                $table->float('purchase_price')->nullable();
                $table->string('image')->nullable();
                $table->string('vat')->nullable();
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

        /* Creating dynamic company based expense and investment table */
        if (!Schema::hasTable('company_'.$company_id.'_expense_and_investments')) {
            Schema::create('company_'.$company_id.'_expense_and_investments', function (Blueprint $table) {
                $table->increments('id');
                $table->string('reference')->nullable();
                $table->string('name');
                $table->float('price');
                $table->float('purchase_price')->nullable();
                $table->string('image')->nullable();
                $table->string('vat')->nullable();
                $table->integer('category_id')->default(0);
                $table->enum('is_active', ['0', '1'])->default('1');
                $table->enum('type', ['expense', 'investment'])->default('expense');
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
                $table->longText('description')->nullable();
                $table->timestamps();
            });
        }

        /* Creating dynamic company based expense categories table */
        if (!Schema::hasTable('company_'.$company_id.'_expense_categories')) {
            Schema::create('company_'.$company_id.'_expense_categories', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->enum('type', ['expense', 'investment'])->dafault('expense');
                $table->timestamps();
            });
        }

        /* Creating dynamic company based rate table */
        if (!Schema::hasTable('company_'.$company_id.'_rates')) {
            Schema::create('company_'.$company_id.'_rates', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->longText('description')->nullable();
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

        /* Creating dynamic company based templates option and value table */
        if (!Schema::hasTable('company_'.$company_id.'_my_template_metas')) {
            Schema::create('company_'.$company_id.'_my_template_metas', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('template_id');
                $table->string('option_name')->nullable();
                $table->longText('option_value')->nullable();
                $table->string('category')->nullable();
                $table->string('type')->nullable();
            });
        }

        /* Creating dynamic company based my templates table */
        if (!Schema::hasTable('company_'.$company_id.'_my_templates')) {
            Schema::create('company_'.$company_id.'_my_templates', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('document_type')->nullable();
                $table->string('font')->nullable();
                $table->string('watermark')->nullable();
                $table->string('color')->nullable();
                $table->enum('design', ['original', 'visual'])->default('original');
                $table->enum('is_default', ['0', '1'])->default('0');
                $table->enum('hide_company_information', ['0', '1'])->default('0');
                $table->enum('hide_assets_information', ['0', '1'])->default('0');
                $table->enum('show_signature_box', ['0', '1'])->default('0');
                $table->timestamps();
            });

            $templates = ['Ordinary Invoice', 'Purchase Delivery Note', 'Purchase Invoice', 'Purchase Order', 'Refund Invoice', 'Sales Delivery Note', 'Sales Estimate', 'Sales Order', 'Work Delivery Note', 'Work Estimate', 'Work Order'];
            foreach($templates as $template){
                MyTemplate::setGlobalTable('company_'.$company_id.'_my_templates');
                $template_created = MyTemplate::create(["name" => $template." Template", "document_type" => $template, "font" => "DejaVu Sans", "color" => "#fd6c00", "is_default" => "1", "hide_company_information" => "0", "hide_assets_information" => "0", "show_signature_box" => "0" ]);
                MyTemplateMeta::setGlobalTable('company_'.$company_id.'_my_template_metas');
                /* Company Information block starts here */
                /* Hide company information */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "hide_company_information_heading",
                    "option_value" => "Hide Company Information",
                    "category" => 'Company Information',
                    "type" => "hide_company_information",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "hide_company_information_show",
                    "option_value" => "1",
                    "category" => 'Company Information',
                    "type" => "hide_company_information",
                ]);

                /* logo */
                MyTemplateMeta::create([
                    "template_id"  => $template_created->id,
                    "option_name"  => "logo_heading",
                    "option_value" => "Logo",
                    "category"     => 'Company Information',
                    "type"         => "logo",
                ]);
                MyTemplateMeta::create([
                    "template_id"  => $template_created->id,
                    "option_name"  => "logo_show",
                    "option_value" => "1",
                    "category"     => 'Company Information',
                    "type"         => "logo",
                ]);

                /* legal name */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "legal_name_heading",
                    "option_value" => "Legal Name",
                    "category" => 'Company Information',
                    "type" => "legal",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "legal_name_show",
                    "option_value" => "1",
                    "category" => 'Company Information',
                    "type" => "legal",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "legal_name_text",
                    "option_value" => "",
                    "category" => 'Company Information',
                    "type" => "legal",
                ]);

                /* name */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "name_heading",
                    "option_value" => "Name",
                    "category" => 'Company Information',
                    "type" => "name",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "name_show",
                    "option_value" => "1",
                    "category" => 'Company Information',
                    "type" => "name",
                ]);

                /* TIN */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "tin_heading",
                    "option_value" => "TIN",
                    "category" => 'Company Information',
                    "type" => "tin",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "tin_show",
                    "option_value" => "1",
                    "category" => 'Company Information',
                    "type" => "tin",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "tin_text",
                    "option_value" => "",
                    "category" => 'Company Information',
                    "type" => "tin",
                ]);

                /* Phone */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "phone_heading",
                    "option_value" => "Phone",
                    "category" => 'Company Information',
                    "type" => "phone",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "phone_show",
                    "option_value" => "1",
                    "category" => 'Company Information',
                    "type" => "phone",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "phone_text",
                    "option_value" => "",
                    "category" => 'Company Information',
                    "type" => "phone",
                ]);

                /* Fax */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "fax_heading",
                    "option_value" => "Fax",
                    "category" => 'Company Information',
                    "type" => "fax",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "fax_show",
                    "option_value" => "1",
                    "category" => 'Company Information',
                    "type" => "fax",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "fax_text",
                    "option_value" => "",
                    "category" => 'Company Information',
                    "type" => "fax",
                ]);

                /* Email */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "email_heading",
                    "option_value" => "Email",
                    "category" => 'Company Information',
                    "type" => "email",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "email_show",
                    "option_value" => "1",
                    "category" => 'Company Information',
                    "type" => "email",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "email_text",
                    "option_value" => "",
                    "category" => 'Company Information',
                    "type" => "email",
                ]);

                /* Website */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "website_heading",
                    "option_value" => "Website",
                    "category" => 'Company Information',
                    "type" => "website",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "website_show",
                    "option_value" => "1",
                    "category" => 'Company Information',
                    "type" => "website",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "website_text",
                    "option_value" => "",
                    "category" => 'Company Information',
                    "type" => "website",
                ]);

                /* Address */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "address_heading",
                    "option_value" => "Address",
                    "category" => 'Company Information',
                    "type" => "address",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "address_show",
                    "option_value" => "1",
                    "category" => 'Company Information',
                    "type" => "address",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "address_text",
                    "option_value" => "",
                    "category" => 'Company Information',
                    "type" => "address",
                ]);

                /* Zip/Postal Code */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "zip_code_heading",
                    "option_value" => "Zip/Postal Code",
                    "category" => 'Company Information',
                    "type" => "zip",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "zip_code_show",
                    "option_value" => "1",
                    "category" => 'Company Information',
                    "type" => "zip",
                ]);

                /* City/Town */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "city_heading",
                    "option_value" => "City/Town Code",
                    "category" => 'Company Information',
                    "type" => "city",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "city_show",
                    "option_value" => "1",
                    "category" => 'Company Information',
                    "type" => "city",
                ]);

                /* State/Province */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "state_heading",
                    "option_value" => "State/Province",
                    "category" => 'Company Information',
                    "type" => "state",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "state_show",
                    "option_value" => "1",
                    "category" => 'Company Information',
                    "type" => "state",
                ]);

                /* Country */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "country_heading",
                    "option_value" => "Country",
                    "category" => 'Company Information',
                    "type" => "country",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "country_show",
                    "option_value" => "1",
                    "category" => 'Company Information',
                    "type" => "country",
                ]);
                /* company information ends here*/


                /* document information starts here */
                /* Document Type */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_type_heading",
                    "option_value" => "Document Type",
                    "category" => "Document Information",
                    "type" => "document_type",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_type_show",
                    "option_value" => "1",
                    "category" => "Document Information",
                    "type" => "document_type",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_type_text",
                    "option_value" => "",
                    "category" => "Document Information",
                    "type" => "document_type",
                ]);

                /* Document Title */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_title_heading",
                    "option_value" => "Document Title",
                    "category" => "Document Information",
                    "type" => "document_title",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_title_show",
                    "option_value" => "1",
                    "category" => "Document Information",
                    "type" => "document_title",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_title_text",
                    "option_value" => "",
                    "category" => "Document Information",
                    "type" => "document_title",
                ]);

                /* Section Title */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_section_title_heading",
                    "option_value" => "Section Title",
                    "category" => "Document Information",
                    "type" => "section",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_section_title_show",
                    "option_value" => "1",
                    "category" => "Document Information",
                    "type" => "section",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_section_title_text",
                    "option_value" => $template." INFO",
                    "category" => "Document Information",
                    "type" => "section",
                ]);

                /* Reference */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_reference_heading",
                    "option_value" => "Reference",
                    "category" => "Document Information",
                    "type" => "reference",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_reference_show",
                    "option_value" => "1",
                    "category" => "Document Information",
                    "type" => "reference",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_reference_text",
                    "option_value" => "Number:",
                    "category" => "Document Information",
                    "type" => "reference",
                ]);

                /* Generated From */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_generated_from_heading",
                    "option_value" => "Generated From",
                    "category" => "Document Information",
                    "type" => "document",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_generated_from_show",
                    "option_value" => "1",
                    "category" => "Document Information",
                    "type" => "document",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_generated_from_text",
                    "option_value" => "Generated From:",
                    "category" => "Document Information",
                    "type" => "document",
                ]);

                /* Date */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_date_heading",
                    "option_value" => "Date",
                    "category" => "Document Information",
                    "type" => "date",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_date_show",
                    "option_value" => "1",
                    "category" => "Document Information",
                    "type" => "date",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_date_text",
                    "option_value" => "Date:",
                    "category" => "Document Information",
                    "type" => "date",
                ]);

                /* Payment Option */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_payment_option_heading",
                    "option_value" => "Payment Option",
                    "category" => "Document Information",
                    "type" => "document_payment",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_payment_option_show",
                    "option_value" => "1",
                    "category" => "Document Information",
                    "type" => "document_payment",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_payment_option_text",
                    "option_value" => "Payment Option:",
                    "category" => "Document Information",
                    "type" => "document_payment",
                ]);

                /* Bank Account */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_bank_account_heading",
                    "option_value" => "Bank Account",
                    "category" => "Document Information",
                    "type" => "document_bank",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_bank_account_show",
                    "option_value" => "1",
                    "category" => "Document Information",
                    "type" => "document_bank",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_bank_account_text",
                    "option_value" => "Account:",
                    "category" => "Document Information",
                    "type" => "document_bank",
                ]);

                /* BIC/SWIFT */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_bic_heading",
                    "option_value" => "BIC/SWIFT",
                    "category" => "Document Information",
                    "type" => "document_bic",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_bic_show",
                    "option_value" => "1",
                    "category" => "Document Information",
                    "type" => "document_bic",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_bic_text",
                    "option_value" => "BIC:",
                    "category" => "Document Information",
                    "type" => "document_bic",
                ]);

                /* Status */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_status_heading",
                    "option_value" => "Status",
                    "category" => "Document Information",
                    "type" => "document_status",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_status_show",
                    "option_value" => "1",
                    "category" => "Document Information",
                    "type" => "document_status",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_status_text",
                    "option_value" => "Status:",
                    "category" => "Document Information",
                    "type" => "document_status",
                ]);

                /* Created By */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_created_by_heading",
                    "option_value" => "Created by",
                    "category" => "Document Information",
                    "type" => "document_created",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_created_by_show",
                    "option_value" => "0",
                    "category" => "Document Information",
                    "type" => "document_created",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_created_by_text",
                    "option_value" => "Created by:",
                    "category" => "Document Information",
                    "type" => "document_created",
                ]);

                /* Agent */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_agent_heading",
                    "option_value" => "Agent",
                    "category" => "Document Information",
                    "type" => "document_agent",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_agent_show",
                    "option_value" => "0",
                    "category" => "Document Information",
                    "type" => "document_agent",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_agent_text",
                    "option_value" => "Agent:",
                    "category" => "Document Information",
                    "type" => "document_agent",
                ]);

                /* Purchase Document Ref. */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "purchase_document_ref_heading",
                    "option_value" => "Purchase Document Ref.",
                    "category" => "Document Information",
                    "type" => "purchase_document",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "purchase_document_ref_show",
                    "option_value" => "1",
                    "category" => "Document Information",
                    "type" => "purchase_document",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "purchase_document_ref_text",
                    "option_value" => "Purchase Document Ref.:",
                    "category" => "Document Information",
                    "type" => "purchase_document",
                ]);

                /* Sent Date: */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_sent_date_heading",
                    "option_value" => "Sent Date",
                    "category" => "Document Information",
                    "type" => "document_sent",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_sent_date_show",
                    "option_value" => "1",
                    "category" => "Document Information",
                    "type" => "document_sent",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_sent_date_text",
                    "option_value" => "Delivery Date:",
                    "category" => "Document Information",
                    "type" => "document_sent",
                ]);

                /* Delivery Option: */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_delivery_option_heading",
                    "option_value" => "Delivery Option",
                    "category" => "Document Information",
                    "type" => "document_delivery",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_delivery_option_show",
                    "option_value" => "1",
                    "category" => "Document Information",
                    "type" => "document_delivery",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "document_delivery_option_text",
                    "option_value" => "Delivery Option:",
                    "category" => "Document Information",
                    "type" => "document_delivery",
                ]);

                /* document information ends here*/

                /* Client/Supplier Information starts here */
                /* Section Title: */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_section_title_heading",
                    "option_value" => "Section Title",
                    "category" => "Client/Supplier Information",
                    "type" => "client_section",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_section_title_show",
                    "option_value" => "1",
                    "category" => "Client/Supplier Information",
                    "type" => "client_section",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_section_title_text",
                    "option_value" => "CLIENT INFO",
                    "category" => "Client/Supplier Information",
                    "type" => "client_section",
                ]);

                /* Reference */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_reference_heading",
                    "option_value" => "Reference",
                    "category" => "Client/Supplier Information",
                    "type" => "client_reference",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_reference_show",
                    "option_value" => "0",
                    "category" => "Client/Supplier Information",
                    "type" => "client_reference",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_reference_text",
                    "option_value" => "",
                    "category" => "Client/Supplier Information",
                    "type" => "client_reference",
                ]);

                /* Legal Name */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_legal_name_heading",
                    "option_value" => "Reference",
                    "category" => "Client/Supplier Information",
                    "type" => "client_legal_name",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_legal_name_show",
                    "option_value" => "1",
                    "category" => "Client/Supplier Information",
                    "type" => "client_legal_name",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_legal_name_text",
                    "option_value" => "",
                    "category" => "Client/Supplier Information",
                    "type" => "client_legal_name",
                ]);

                /* Name */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_name_heading",
                    "option_value" => "Name",
                    "category" => "Client/Supplier Information",
                    "type" => "client_name",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_name_show",
                    "option_value" => "1",
                    "category" => "Client/Supplier Information",
                    "type" => "client_name",
                ]);

                /* TIN */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_tin_heading",
                    "option_value" => "TIN",
                    "category" => "Client/Supplier Information",
                    "type" => "client_tin",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_tin_show",
                    "option_value" => "1",
                    "category" => "Client/Supplier Information",
                    "type" => "client_tin",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_tin_text",
                    "option_value" => "",
                    "category" => "Client/Supplier Information",
                    "type" => "client_tin",
                ]);

                /* Phone */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_phone_heading",
                    "option_value" => "Phone",
                    "category" => "Client/Supplier Information",
                    "type" => "client_phone",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_phone_show",
                    "option_value" => "1",
                    "category" => "Client/Supplier Information",
                    "type" => "client_phone",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_phone_text",
                    "option_value" => "Phone:",
                    "category" => "Client/Supplier Information",
                    "type" => "client_phone",
                ]);

                /* Fax */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_fax_heading",
                    "option_value" => "Phone",
                    "category" => "Client/Supplier Information",
                    "type" => "client_fax",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_fax_show",
                    "option_value" => "0",
                    "category" => "Client/Supplier Information",
                    "type" => "client_fax",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_fax_text",
                    "option_value" => "Fax:",
                    "category" => "Client/Supplier Information",
                    "type" => "client_fax",
                ]);

                /* Email */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_email_heading",
                    "option_value" => "Email",
                    "category" => "Client/Supplier Information",
                    "type" => "client_supplier",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_email_show",
                    "option_value" => "0",
                    "category" => "Client/Supplier Information",
                    "type" => "client_supplier",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_email_text",
                    "option_value" => "",
                    "category" => "Client/Supplier Information",
                    "type" => "client_supplier",
                ]);

                /* Website */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_website_heading",
                    "option_value" => "Email",
                    "category" => "Client/Supplier Information",
                    "type" => "client_website",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_website_show",
                    "option_value" => "0",
                    "category" => "Client/Supplier Information",
                    "type" => "client_website",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_website_text",
                    "option_value" => "",
                    "category" => "Client/Supplier Information",
                    "type" => "client_website",
                ]);

                /* Billing Address */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_billing_address_heading",
                    "option_value" => "Billing Address",
                    "category" => "Client/Supplier Information",
                    "type" => "client_billing",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_billing_address_show",
                    "option_value" => "1",
                    "category" => "Client/Supplier Information",
                    "type" => "client_billing",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_billing_address_text",
                    "option_value" => "",
                    "category" => "Client/Supplier Information",
                    "type" => "client_billing",
                ]);

                /* Zip/Postal Code */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_zip_code_heading",
                    "option_value" => "Zip/Postal Code",
                    "category" => "Client/Supplier Information",
                    "type" => "client_zip_code",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_zip_code_show",
                    "option_value" => "1",
                    "category" => "Client/Supplier Information",
                    "type" => "client_zip_code",
                ]);

                /* City/Town */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_city_heading",
                    "option_value" => "City/Town Code",
                    "category" => "Client/Supplier Information",
                    "type" => "client_city",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_city_show",
                    "option_value" => "1",
                    "category" => "Client/Supplier Information",
                    "type" => "client_city",
                ]);

                /* State/Province */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_state_heading",
                    "option_value" => "State/Province",
                    "category" => "Client/Supplier Information",
                    "type" => "client_state",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_state_show",
                    "option_value" => "1",
                    "category" => "Client/Supplier Information",
                    "type" => "client_state",
                ]);

                /* Country */
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_country_heading",
                    "option_value" => "Country",
                    "category" => "Client/Supplier Information",
                    "type" => "client_country",
                ]);
                MyTemplateMeta::create([
                    "template_id" => $template_created->id,
                    "option_name" => "client_country_show",
                    "option_value" => "1",
                    "category" => "Client/Supplier Information",
                    "type" => "client_country",
                ]);
                /* Client/Supplier Information ends here */

                /* Items starts here */
                    /* Reference column */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_reference_column_heading",
                        "option_value" => "Reference Col.",
                        "category" => "Items",
                        "type" => "items_reference",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_reference_column_show",
                        "option_value" => "1",
                        "category" => "Items",
                        "type" => "items_reference",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_reference_column_text",
                        "option_value" => "REF.",
                        "category" => "Items",
                        "type" => "items_reference",
                    ]);

                    /* Barcode */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_barcode_heading",
                        "option_value" => "Barcode",
                        "category" => "Items",
                        "type" => "items_barcode",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_barcode_show",
                        "option_value" => "1",
                        "category" => "Items",
                        "type" => "items_barcode",
                    ]);

                    /* Name column */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_name_column_heading",
                        "option_value" => "Name Col.",
                        "category" => "Items",
                        "type" => "items_name",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_name_column_show",
                        "option_value" => "1",
                        "category" => "Items",
                        "type" => "items_name",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_name_column_text",
                        "option_value" => "NAME",
                        "category" => "Items",
                        "type" => "items_name",
                    ]);

                    /* Description */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_description_heading",
                        "option_value" => "Description",
                        "category" => "Items",
                        "type" => "items_description",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_description_show",
                        "option_value" => "1",
                        "category" => "Items",
                        "type" => "items_description",
                    ]);

                    /* Unit Price column */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_unit_price_column_heading",
                        "option_value" => "Unit Price Col.",
                        "category" => "Items",
                        "type" => "items_unit_price",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_unit_price_column_show",
                        "option_value" => "1",
                        "category" => "Items",
                        "type" => "items_unit_price",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_unit_price_column_text",
                        "option_value" => "PRICE",
                        "category" => "Items",
                        "type" => "items_unit_price",
                    ]);


                    /* Discount column */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_discount_column_heading",
                        "option_value" => "Discount Col.",
                        "category" => "Items",
                        "type" => "items_discount",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_discount_column_show",
                        "option_value" => "1",
                        "category" => "Items",
                        "type" => "items_discount",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_discount_column_text",
                        "option_value" => "DISC.",
                        "category" => "Items",
                        "type" => "items_discount",
                    ]);

                    /* Units column */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_units_column_heading",
                        "option_value" => "Units Col.",
                        "category" => "Items",
                        "type" => "items_units",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_units_column_show",
                        "option_value" => "1",
                        "category" => "Items",
                        "type" => "items_units",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_units_column_text",
                        "option_value" => "QTY.",
                        "category" => "Items",
                        "type" => "items_units",
                    ]);

                    /* Price column */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_price_column_heading",
                        "option_value" => "Price Col.",
                        "category" => "Items",
                        "type" => "items_price",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_price_column_show",
                        "option_value" => "1",
                        "category" => "Items",
                        "type" => "items_price",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_price_column_text",
                        "option_value" => "SUBTOTAL",
                        "category" => "Items",
                        "type" => "items_price",
                    ]);

                    /* Tax column */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_tax_column_heading",
                        "option_value" => "Tax Col.",
                        "category" => "Items",
                        "type" => "items_tax",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_tax_column_show",
                        "option_value" => "1",
                        "category" => "Items",
                        "type" => "items_tax",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_tax_column_text",
                        "option_value" => "TAXES",
                        "category" => "Items",
                        "type" => "items_tax",
                    ]);

                    /* Discount text */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_discount_heading",
                        "option_value" => "Discount text",
                        "category" => "Items",
                        "type" => "discount",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_discount_text",
                        "option_value" => "Disc.:",
                        "category" => "Items",
                        "type" => "discount",
                    ]);

                    /* Subtotal text */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_subtotal_text_heading",
                        "option_value" => "Subtotal text",
                        "category" => "Items",
                        "type" => "items_subtotal",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_subtotal_text_text",
                        "option_value" => "Subtotal:",
                        "category" => "Items",
                        "type" => "items_subtotal",
                    ]);

                    /* Discount line */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_discount_line_heading",
                        "option_value" => "Discount line",
                        "category" => "Items",
                        "type" => "items_discount_line",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_discount_line_text",
                        "option_value" => "Discount on subtotal:",
                        "category" => "Items",
                        "type" => "items_discount_line",
                    ]);

                /* Items ends here */

                /* Signature and Summary starts here */
                    /* Signature title */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "sign_signature_title_heading",
                        "option_value" => "Signature Title",
                        "category" => "Signature and Summary",
                        "type" => "sign_signature_title",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "sign_signature_title_show",
                        "option_value" => "1",
                        "category" => "Signature and Summary",
                        "type" => "sign_signature_title",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "sign_signature_title_text",
                        "option_value" => "Signed:",
                        "category" => "Signature and Summary",
                        "type" => "sign_signature_title",
                    ]);

                    /* Signature Name */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "sign_signature_name_heading",
                        "option_value" => "Signature Name",
                        "category" => "Signature and Summary",
                        "type" => "sign_signature_name",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "sign_signature_name_show",
                        "option_value" => "1",
                        "category" => "Signature and Summary",
                        "type" => "sign_signature_name",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "sign_signature_name_text",
                        "option_value" => "Name:",
                        "category" => "Signature and Summary",
                        "type" => "sign_signature_name",
                    ]);

                    /* TIN Signature */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "sign_tin_signature_heading",
                        "option_value" => "Signature Name",
                        "category" => "Signature and Summary",
                        "type" => "sign_tin_signature",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "sign_tin_signature_show",
                        "option_value" => "1",
                        "category" => "Signature and Summary",
                        "type" => "sign_tin_signature",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "sign_tin_signature_text",
                        "option_value" => "TIN:",
                        "category" => "Signature and Summary",
                        "type" => "sign_tin_signature",
                    ]);

                    /* Base Text */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "sign_base_text_heading",
                        "option_value" => "Base text",
                        "category" => "Signature and Summary",
                        "type" => "sign_base_text",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "sign_base_text_text",
                        "option_value" => "BASE",
                        "category" => "Signature and Summary",
                        "type" => "sign_base_text",
                    ]);

                    /* Total Text */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "sign_total_text_heading",
                        "option_value" => "Total text",
                        "category" => "Signature and Summary",
                        "type" => "sign_total_text",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "sign_total_text_text",
                        "option_value" => "TOTAL",
                        "category" => "Signature and Summary",
                        "type" => "sign_total_text",
                    ]);


                /* Signature and Summary ends here */

                /* Footer and Legal Note starts here */
                    /* Footer */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "footer_heading",
                        "option_value" => "Footer",
                        "category" => "Footer and Legal Note",
                        "type" => "footer_heading",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "footer_show",
                        "option_value" => "0",
                        "category" => "Footer and Legal Note",
                        "type" => "footer_heading",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "footer_text",
                        "option_value" => "",
                        "category" => "Footer and Legal Note",
                        "type" => "footer_heading",
                    ]);

                    /* Legal Note */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "legal_note_heading",
                        "option_value" => "Legal Note",
                        "category" => "Footer and Legal Note",
                        "type" => "legal_note",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "legal_note_show",
                        "option_value" => "Down",
                        "category" => "Footer and Legal Note",
                        "type" => "legal_note",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "legal_note_text",
                        "option_value" => "",
                        "category" => "Footer and Legal Note",
                        "type" => "legal_note",
                    ]);

                /* Footer and Legal Note ends here */

                /* Comments and Addendums starts here */
                    /* Comments */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "comments_heading",
                        "option_value" => "Comments",
                        "category" => "Comments and Addendums",
                        "type" => "comments",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "comments_show",
                        "option_value" => "1",
                        "category" => "Comments and Addendums",
                        "type" => "comments",
                    ]);

                    /* Comments Title */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "comments_title_heading",
                        "option_value" => "Comments",
                        "category" => "Comments and Addendums",
                        "type" => "comments_title",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "comments_title_show",
                        "option_value" => "1",
                        "category" => "Comments and Addendums",
                        "type" => "comments_title",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "comments_title_text",
                        "option_value" => "COMMENTS",
                        "category" => "Comments and Addendums",
                        "type" => "comments_title",
                    ]);

                    /* Addendum */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "addendum_heading",
                        "option_value" => "Addendum",
                        "category" => "Comments and Addendums",
                        "type" => "addendum",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "addendum_show",
                        "option_value" => "1",
                        "category" => "Comments and Addendums",
                        "type" => "addendum",
                    ]);

                    /* Addendum Title*/
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "addendum_title_heading",
                        "option_value" => "Addendum",
                        "category" => "Comments and Addendums",
                        "type" => "addendum_title",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "addendum_title_show",
                        "option_value" => "1",
                        "category" => "Comments and Addendums",
                        "type" => "addendum_title",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "addendum_title_text",
                        "option_value" => "ADDENDUM",
                        "category" => "Comments and Addendums",
                        "type" => "addendum_title",
                    ]);

                    /* Addendum as image*/
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "addendum_as_image_heading",
                        "option_value" => "Addendum",
                        "category" => "Comments and Addendums",
                        "type" => "addendum_as_image",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "addendum_as_image_show",
                        "option_value" => "1",
                        "category" => "Comments and Addendums",
                        "type" => "addendum_as_image",
                    ]);


                /* Comments and Addendums ends here */

                /* Client Assets starts here */
                if(in_array($template, ['Ordinary Invoice', 'Refund Invoice', 'Work Delivery Note', 'Work Estimate', 'Work Order'])){
                        /* Section Title */
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_section_title_heading",
                            "option_value" => "Section Title",
                            "category" => "Client Assets",
                            "type" => "client_assets_section",
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_section_title_show",
                            "option_value" => "1",
                            "category" => "Client Assets",
                            "type" => "client_assets_section",
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_section_title_text",
                            "option_value" => "ASSETS INFO",
                            "category" => "Client Assets",
                            "type" => "client_assets_section",
                        ]);

                        /* Image */
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_image_heading",
                            "option_value" => "Image",
                            "category" => "Client Assets",
                            "type" => "client_assets_image",
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_image_show",
                            "option_value" => "1",
                            "category" => "Client Assets",
                            "type" => "client_assets_image",
                        ]);

                        /* Reference */
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_reference_heading",
                            "option_value" => "Reference",
                            "category" => "Client Assets",
                            "type" => "client_assets_reference",
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_reference_show",
                            "option_value" => "0",
                            "category" => "Client Assets",
                            "type" => "client_assets_reference",
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_reference_text",
                            "option_value" => "",
                            "category" => "Client Assets",
                            "type" => "client_assets_reference",
                        ]);

                        /* Name */
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_name_heading",
                            "option_value" => "Name",
                            "category" => "Client Assets",
                            "type" => "client_assets_name",
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_name_show",
                            "option_value" => "1",
                            "category" => "Client Assets",
                            "type" => "client_assets_name",
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_name_text",
                            "option_value" => "NAME:",
                            "category" => "Client Assets",
                            "type" => "client_assets_name",
                        ]);

                        /* Identifier */
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_identifier_heading",
                            "option_value" => "Identifier",
                            "category" => "Client Assets",
                            "type" => "client_assets_identifier",
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_identifier_show",
                            "option_value" => "1",
                            "category" => "Client Assets",
                            "type" => "client_assets_identifier",
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_identifier_text",
                            "option_value" => "Identifier:",
                            "category" => "Client Assets",
                            "type" => "client_assets_identifier",
                        ]);


                        /* Serial Number */
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_serial_no_heading",
                            "option_value" => "Serial Number",
                            "category" => "Client Assets",
                            "type" => "client_assets_serial_no",
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_serial_no_show",
                            "option_value" => "1",
                            "category" => "Client Assets",
                            "type" => "client_assets_serial_no",
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_serial_no_text",
                            "option_value" => "Serial Number:",
                            "category" => "Client Assets",
                            "type" => "client_assets_serial_no",
                        ]);

                        /* Brand */
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_brand_heading",
                            "option_value" => "Brand",
                            "category" => "Client Assets",
                            "type" => "client_assets_brand",
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_brand_show",
                            "option_value" => "1",
                            "category" => "Client Assets",
                            "type" => "client_assets_brand",
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_brand_text",
                            "option_value" => "Brand:",
                            "category" => "Client Assets",
                            "type" => "client_assets_brand",
                        ]);

                        /* Model */
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_model_heading",
                            "option_value" => "Brand",
                            "category" => "Client Assets",
                            "type" => "client_assets_model",
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_model_show",
                            "option_value" => "1",
                            "category" => "Client Assets",
                            "type" => "client_assets_model",
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_model_text",
                            "option_value" => "Brand:",
                            "category" => "Client Assets",
                            "type" => "client_assets_model",
                        ]);

                        /* Start of the Warranty */
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_start_warranty_heading",
                            "option_value" => "Start of the Warranty",
                            "category" => "Client Assets",
                            "type" => "client_assets_start_warranty",
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_start_warranty_show",
                            "option_value" => "0",
                            "category" => "Client Assets",
                            "type" => "client_assets_start_warranty",
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_start_warranty_text",
                            "option_value" => "",
                            "category" => "Client Assets",
                            "type" => "client_assets_start_warranty",
                        ]);

                        /* End of the Warranty */
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_end_warranty_heading",
                            "option_value" => "End of the Warranty",
                            "category" => "Client Assets",
                            "type" => "client_assets_end_warranty",
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_end_warranty_show",
                            "option_value" => "0",
                            "category" => "Client Assets",
                            "type" => "client_assets_end_warranty",
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_end_warranty_text",
                            "option_value" => "",
                            "category" => "Client Assets",
                            "type" => "client_assets_end_warranty",
                        ]);

                        /* Description */
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_description_heading",
                            "option_value" => "End of the Warranty",
                            "category" => "Client Assets",
                            "type" => "client_assets_description",
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_description_show",
                            "option_value" => "0",
                            "category" => "Client Assets",
                            "type" => "client_assets_description",
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_end_description_text",
                            "option_value" => "",
                            "category" => "Client Assets",
                            "type" => "client_assets_description",
                        ]);
                        
                        
                    /* Client Assets ends here */
                }

                if(in_array($template, ['Ordinary Invoice', 'Purchase Invoice', 'Refund Invoice'])){
                    /* Payment Terms starts here */
                    /* Payment Terms */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "payment_terms_heading",
                        "option_value" => "Payment Terms",
                        "category" => "Payment Terms",
                        "type" => "payment_terms",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "payment_terms_show",
                        "option_value" => "1",
                        "category" => "Payment Terms",
                        "type" => "payment_terms",
                    ]);

                    /* Payment Terms Title  */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "payment_terms_title_heading",
                        "option_value" => "Payment Terms",
                        "category" => "Payment Terms",
                        "type" => "payment_terms_title",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "payment_terms_title_show",
                        "option_value" => "1",
                        "category" => "Payment Terms",
                        "type" => "payment_terms_title",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "payment_terms_title_text",
                        "option_value" => "Payment Terms:",
                        "category" => "Payment Terms",
                        "type" => "payment_terms_title",
                    ]);

                    /* Date Column  */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "payment_terms_date_heading",
                        "option_value" => "Date Col.",
                        "category" => "Payment Terms",
                        "type" => "payment_terms_date",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "payment_terms_date_text",
                        "option_value" => "DATE",
                        "category" => "Payment Terms",
                        "type" => "payment_terms_date",
                    ]);

                    /* Amount Column  */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "payment_terms_amount_heading",
                        "option_value" => "Amount Col.",
                        "category" => "Payment Terms",
                        "type" => "payment_terms_amount",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "payment_terms_amount_text",
                        "option_value" => "AMOUNT",
                        "category" => "Payment Terms",
                        "type" => "payment_terms_amount",
                    ]);

                    /* Paid Column  */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "payment_terms_paid_heading",
                        "option_value" => "Paid Col.",
                        "category" => "Payment Terms",
                        "type" => "payment_terms_paid",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "payment_terms_paid_show",
                        "option_value" => "1",
                        "category" => "Payment Terms",
                        "type" => "payment_terms_paid",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "payment_terms_paid_text",
                        "option_value" => "PAID",
                        "category" => "Payment Terms",
                        "type" => "payment_terms_paid",
                    ]);

                    /* Paid text  */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "payment_terms_paid_text_heading",
                        "option_value" => "Paid text",
                        "category" => "Payment Terms",
                        "type" => "payment_terms_paid_text",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "payment_terms_paid_text_text",
                        "option_value" => "Yes",
                        "category" => "Payment Terms",
                        "type" => "payment_terms_paid_text",
                    ]);
                   
                    /* Unpaid text  */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "payment_terms_unpaid_text_heading",
                        "option_value" => "Unpaid text",
                        "category" => "Payment Terms",
                        "type" => "payment_terms_unpaid_text",
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "payment_terms_unpaid_text_text",
                        "option_value" => "No",
                        "category" => "Payment Terms",
                        "type" => "payment_terms_unpaid_text",
                    ]);

                }
            }
        }

        /* Creating dynamic company based settings table */
        if (!Schema::hasTable('company_'.$company_id.'_settings')) {
            Schema::create('company_'.$company_id.'_settings', function (Blueprint $table) {
                $table->increments('id');
                $table->string('option_name')->nullabe();
                $table->longText('option_value')->nullabe();
                $table->timestamps();
            }); 

            /* Add entries in settings table */
        Setting::setGlobalTable('company_'.$company_id.'_settings');
        /* Email send as */
        Setting::create([
            "option_name" => "email_configuration_send_as",
            "option_value" => "STEL Order Email Address",
        ]);
        /* Email sender name */
        Setting::create([
            "option_name" => "email_configuration_sender_name",
            "option_value" => "",
        ]);
        /* Email send copy to */
        Setting::create([
            "option_name" => "email_configuration_send_copy_to",
            "option_value" => "Without copy",
        ]);
        /* Email connect */
        Setting::create([
            "option_name" => "email_configuration_email_connect",
            "option_value" => "",
        ]);
        /* Email reply to */
        Setting::create([
            "option_name" => "email_configuration_reply_to",
            "option_value" => "Sending Address",
        ]);
        /* Email send read receipts to */
        Setting::create([
            "option_name" => "email_configuration_send_read_receipts_to",
            "option_value" => "Sending Address",
        ]);
        /* Email client emails subject */
        Setting::create([
            "option_name" => "email_configuration_client_emails_subject",
            "option_value" => "@CLIENTNAME@, you can now access your @DOCUMENTTYPE@ from @MYCOMPANY@",
        ]);
        /* Email client emails message */
        Setting::create([
            "option_name" => "email_configuration_client_emails_message",
            "option_value" => "Esteemed @CLIENTNAME@,



You will find your @DOCUMENTTYPE@ attached to this email.



Best regards and thank you for placing your trust in @MYCOMPANY@.

@USERNAME@",
        ]);
        /* Email signature */
        Setting::create([
            "option_name" => "email_configuration_signature",
            "option_value" => "",
        ]);
        }

        
        /* Creating dynamic company based default pdf options table */
        if (!Schema::hasTable('company_'.$company_id.'_default_pdf_send_options')) {
            Schema::create('company_'.$company_id.'_default_pdf_send_options', function (Blueprint $table) {
                $table->increments('id');
                $table->string('type');
                $table->string('format');
                $table->enum('price_after_tax', ['0', '1'])->default('0');
                $table->enum('mailing_format', ['0', '1'])->default('0');
                $table->enum('include_main_image', ['0', '1'])->default('0');
                $table->timestamps();
            }); 

            DefaultPdfSendOption::setGlobalTable('company_'.$company_id.'_default_pdf_send_options');

            $templates = ['Ordinary Invoice', 'Purchase Delivery Note', 'Purchase Invoice', 'Purchase Order', 'Refund Invoice', 'Sales Delivery Note', 'Sales Estimate', 'Sales Order', 'Work Delivery Note', 'Work Estimate', 'Work Order'];

            foreach($templates as $template){
                DefaultPdfSendOption::create([
                    "type" => $template,
                    "format" => "Valued",
                    "price_after_tax" => "0",
                    "mailing_format" => "0",
                    "include_main_image" => "0",
                ]);
            }
        }

        if (!Schema::hasColumn('company_'.$company_id.'_roles', 'guard_name')){
            Schema::table('company_'.$company_id.'_roles', function (Blueprint $table) {
                $table->string('guard_name')->nullable();
            });
        }

        if (!Schema::hasColumn('company_'.$company_id.'_permissions', 'guard_name')){
            Schema::table('company_'.$company_id.'_permissions', function (Blueprint $table) {
                $table->string('guard_name')->nullable();
            });
        }

        if (!Schema::hasColumn('company_'.$company_id.'_rates', 'description')){
            Schema::table('company_'.$company_id.'_rates', function (Blueprint $table) {
                $table->string('description')->nullable();
            });
        }

        if (!Schema::hasColumn('company_'.$company_id.'_permissions', 'parent_id')){
            Schema::table('company_'.$company_id.'_permissions', function (Blueprint $table) {
                $table->integer('parent_id')->default('0');
                $table->integer('is_checkbox')->default('0');
            });
        }

        /* Create roles */

        if (!Permission::where('name', 'Visible Roles')->exists()) {
           $permission = Permission::create(['name' => 'Visible Roles']);
           $permission->parent_id = 0;
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Admin')->exists()) {
           $permission = Permission::create(['name' => 'Admin']);
           $permission->parent_id = Permission::where('name' ,'Visible Roles')->pluck('id')->first();
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Sales Admin')->exists()) {
            $permission = Permission::create(['name' => 'Sales Admin']);
            $permission->parent_id = Permission::where('name' ,'Visible Roles')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'Salesperson')->exists()) {
            $permission = Permission::create(['name' => 'Salesperson']);
            $permission->parent_id = Permission::where('name' ,'Visible Roles')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }
        
        if (!Permission::where('name', 'Technical Admin')->exists()) {
            $permission = Permission::create(['name' => 'Technical Admin']);
            $permission->parent_id = Permission::where('name' ,'Visible Roles')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'Technician')->exists()) {
            $permission = Permission::create(['name' => 'Technician']);
            $permission->parent_id = Permission::where('name' ,'Visible Roles')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* Create Permissions */

        /* parent permissions */

        


        if (!Permission::where('name', 'Permissions')->exists()) {
           $permission = Permission::create(['name' => 'Permissions']);
           $permission->parent_id = 0;
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Access')->exists()) {
           $permission = Permission::create(['name' => 'Access']);
           $permission->parent_id = 0;
           $permission->is_checkbox = 0;
           $permission->save();
        }


        if (!Permission::where('name', 'Home')->exists()) {
           $permission = Permission::create(['name' => 'Home']);
           $permission->parent_id = 0;
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Catalog')->exists()) {
           $permission = Permission::create(['name' => 'Catalog']);
           $permission->parent_id = 0;
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Clients')->exists()) {
           $permission = Permission::create(['name' => 'Clients']);
           $permission->parent_id = 0;
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Sales')->exists()) {
           $permission = Permission::create(['name' => 'Sales']);
           $permission->parent_id = 0;
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Technical Service')->exists()) {
           $permission = Permission::create(['name' => 'Technical Service']);
           $permission->parent_id = 0;
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Invoicing')->exists()) {
           $permission = Permission::create(['name' => 'Invoicing']);
           $permission->parent_id = 0;
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Purchases')->exists()) {
           $permission = Permission::create(['name' => 'Purchases']);
           $permission->parent_id = 0;
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Calendar')->exists()) {
           $permission = Permission::create(['name' => 'Calendar']);
           $permission->parent_id = 0;
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Reports')->exists()) {
           $permission = Permission::create(['name' => 'Reports']);
           $permission->parent_id = 0;
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Profile')->exists()) {
           $permission = Permission::create(['name' => 'Profile']);
           $permission->parent_id = 0;
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Connect')->exists()) {
           $permission = Permission::create(['name' => 'Connect']);
           $permission->parent_id = 0;
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Settings')->exists()) {
           $permission = Permission::create(['name' => 'Settings']);
           $permission->parent_id = 0;
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Other Configuration')->exists()) {
           $permission = Permission::create(['name' => 'Other Configuration']);
           $permission->parent_id = 0;
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Products')->exists()) {
           $permission = Permission::create(['name' => 'Products']);
           $permission->parent_id = Permission::where('name' ,'Catalog')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Services')->exists()) {
           $permission = Permission::create(['name' => 'Services']);
           $permission->parent_id = Permission::where('name' ,'Catalog')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Expenses & Investments')->exists()) {
           $permission = Permission::create(['name' => 'Expenses & Investments']);
           $permission->parent_id = Permission::where('name' ,'Catalog')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Client Assets')->exists()) {
           $permission = Permission::create(['name' => 'Client Assets']);
           $permission->parent_id = Permission::where('name' ,'Catalog')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Client ')->exists()) {
           $permission = Permission::create(['name' => 'Client ']);
           $permission->parent_id = Permission::where('name' ,'Clients')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Potential Clients')->exists()) {
           $permission = Permission::create(['name' => 'Potential Clients']);
           $permission->parent_id = Permission::where('name' ,'Clients')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Contacts')->exists()) {
           $permission = Permission::create(['name' => 'Contacts']);
           $permission->parent_id = Permission::where('name' ,'Clients')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Client bank account')->exists()) {
           $permission = Permission::create(['name' => 'Client bank account']);
           $permission->parent_id = Permission::where('name' ,'Clients')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Estimates')->exists()) {
           $permission = Permission::create(['name' => 'Estimates']);
           $permission->parent_id = Permission::where('name' ,'Sales')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Orders')->exists()) {
           $permission = Permission::create(['name' => 'Orders']);
           $permission->parent_id = Permission::where('name' ,'Sales')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Delivery Notes')->exists()) {
           $permission = Permission::create(['name' => 'Delivery Notes']);
           $permission->parent_id = Permission::where('name' ,'Sales')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Incidents')->exists()) {
           $permission = Permission::create(['name' => 'Incidents']);
           $permission->parent_id = Permission::where('name' ,'Technical Service')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Work Estimate')->exists()) {
           $permission = Permission::create(['name' => 'Work Estimate']);
           $permission->parent_id = Permission::where('name' ,'Technical Service')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Work Orders')->exists()) {
           $permission = Permission::create(['name' => 'Work Orders']);
           $permission->parent_id = Permission::where('name' ,'Technical Service')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Work Delivery Notes')->exists()) {
           $permission = Permission::create(['name' => 'Work Delivery Notes']);
           $permission->parent_id = Permission::where('name' ,'Technical Service')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Invoices')->exists()) {
           $permission = Permission::create(['name' => 'Invoices']);
           $permission->parent_id = Permission::where('name' ,'Invoicing')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Refunds')->exists()) {
           $permission = Permission::create(['name' => 'Refunds']);
           $permission->parent_id = Permission::where('name' ,'Invoicing')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Ordinary Invoice Receipts')->exists()) {
           $permission = Permission::create(['name' => 'Ordinary Invoice Receipts']);
           $permission->parent_id = Permission::where('name' ,'Invoicing')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Refund Receipts')->exists()) {
           $permission = Permission::create(['name' => 'Refund Receipts']);
           $permission->parent_id = Permission::where('name' ,'Invoicing')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Invoice Summary')->exists()) {
           $permission = Permission::create(['name' => 'Invoice Summary']);
           $permission->parent_id = Permission::where('name' ,'Invoicing')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Tickets and other expenses')->exists()) {
           $permission = Permission::create(['name' => 'Tickets and other expenses']);
           $permission->parent_id = Permission::where('name' ,'Purchases')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Purchase Orders')->exists()) {
           $permission = Permission::create(['name' => 'Purchase Orders']);
           $permission->parent_id = Permission::where('name' ,'Purchases')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Purchase Delivery Notes')->exists()) {
           $permission = Permission::create(['name' => 'Purchase Delivery Notes']);
           $permission->parent_id = Permission::where('name' ,'Purchases')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Purchase Invoices')->exists()) {
           $permission = Permission::create(['name' => 'Purchase Invoices']);
           $permission->parent_id = Permission::where('name' ,'Purchases')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Purchase Invoice Receipts')->exists()) {
           $permission = Permission::create(['name' => 'Purchase Invoice Receipts']);
           $permission->parent_id = Permission::where('name' ,'Purchases')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Purchase Invoice Summary')->exists()) {
           $permission = Permission::create(['name' => 'Purchase Invoice Summary']);
           $permission->parent_id = Permission::where('name' ,'Purchases')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Suppliers')->exists()) {
           $permission = Permission::create(['name' => 'Suppliers']);
           $permission->parent_id = Permission::where('name' ,'Purchases')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Supplier Bank Account')->exists()) {
           $permission = Permission::create(['name' => 'Supplier Bank Account']);
           $permission->parent_id = Permission::where('name' ,'Purchases')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }


        if (!Permission::where('name', 'Calendar')->exists()) {
           $permission = Permission::create(['name' => 'Calendar']);
           $permission->parent_id = Permission::where('name' ,'Calendar')->pluck('id')->first();
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Show all events in the "Related" section')->exists()) {
           $permission = Permission::create(['name' => 'Show all events in the "Related" section']);
           $permission->parent_id = Permission::where('name' ,'Calendar')->pluck('id')->first();
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Tasks')->exists()) {
           $permission = Permission::create(['name' => 'Tasks']);
           $permission->parent_id = Permission::where('name' ,'Calendar')->pluck('id')->first();
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Overview')->exists()) {
           $permission = Permission::create(['name' => 'Overview']);
           $permission->parent_id = Permission::where('name' ,'Reports')->pluck('id')->first();
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Invoicing by Client')->exists()) {
           $permission = Permission::create(['name' => 'Invoicing by Client']);
           $permission->parent_id = Permission::where('name' ,'Reports')->pluck('id')->first();
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Invoicing by Agent')->exists()) {
           $permission = Permission::create(['name' => 'Invoicing by Agent']);
           $permission->parent_id = Permission::where('name' ,'Reports')->pluck('id')->first();
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Invoicing by Item')->exists()) {
           $permission = Permission::create(['name' => 'Invoicing by Item']);
           $permission->parent_id = Permission::where('name' ,'Reports')->pluck('id')->first();
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Cash Flow Overview')->exists()) {
           $permission = Permission::create(['name' => 'Cash Flow Overview']);
           $permission->parent_id = Permission::where('name' ,'Reports')->pluck('id')->first();
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Cash Flow by Payment Options')->exists()) {
           $permission = Permission::create(['name' => 'Cash Flow by Payment Options']);
           $permission->parent_id = Permission::where('name' ,'Reports')->pluck('id')->first();
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Cash Flow by Agent')->exists()) {
           $permission = Permission::create(['name' => 'Cash Flow by Agent']);
           $permission->parent_id = Permission::where('name' ,'Reports')->pluck('id')->first();
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Sales Overview')->exists()) {
           $permission = Permission::create(['name' => 'Sales Overview']);
           $permission->parent_id = Permission::where('name' ,'Reports')->pluck('id')->first();
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Sales by Client')->exists()) {
           $permission = Permission::create(['name' => 'Sales by Client']);
           $permission->parent_id = Permission::where('name' ,'Reports')->pluck('id')->first();
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Sales by Agent')->exists()) {
           $permission = Permission::create(['name' => 'Sales by Agent']);
           $permission->parent_id = Permission::where('name' ,'Reports')->pluck('id')->first();
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Sales by Item')->exists()) {
           $permission = Permission::create(['name' => 'Sales by Item']);
           $permission->parent_id = Permission::where('name' ,'Reports')->pluck('id')->first();
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Technical Service Overview')->exists()) {
           $permission = Permission::create(['name' => 'Technical Service Overview']);
           $permission->parent_id = Permission::where('name' ,'Reports')->pluck('id')->first();
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Incidents by Client')->exists()) {
           $permission = Permission::create(['name' => 'Incidents by Client']);
           $permission->parent_id = Permission::where('name' ,'Reports')->pluck('id')->first();
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Incidents by Agent')->exists()) {
           $permission = Permission::create(['name' => 'Incidents by Agent']);
           $permission->parent_id = Permission::where('name' ,'Reports')->pluck('id')->first();
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Technical Service by Client')->exists()) {
           $permission = Permission::create(['name' => 'Technical Service by Client']);
           $permission->parent_id = Permission::where('name' ,'Reports')->pluck('id')->first();
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Technical Service by Agent')->exists()) {
           $permission = Permission::create(['name' => 'Technical Service by Agent']);
           $permission->parent_id = Permission::where('name' ,'Reports')->pluck('id')->first();
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Technical Service by Item')->exists()) {
           $permission = Permission::create(['name' => 'Technical Service by Item']);
           $permission->parent_id = Permission::where('name' ,'Reports')->pluck('id')->first();
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Purchases by Provider')->exists()) {
           $permission = Permission::create(['name' => 'Purchases by Provider']);
           $permission->parent_id = Permission::where('name' ,'Reports')->pluck('id')->first();
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Purchases by Item')->exists()) {
           $permission = Permission::create(['name' => 'Purchases by Item']);
           $permission->parent_id = Permission::where('name' ,'Reports')->pluck('id')->first();
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Stock Valuation')->exists()) {
           $permission = Permission::create(['name' => 'Stock Valuation']);
           $permission->parent_id = Permission::where('name' ,'Reports')->pluck('id')->first();
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'View tax reports')->exists()) {
           $permission = Permission::create(['name' => 'View tax reports']);
           $permission->parent_id = Permission::where('name' ,'Reports')->pluck('id')->first();
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Document Management')->exists()) {
           $permission = Permission::create(['name' => 'Document Management']);
           $permission->parent_id = Permission::where('name' ,'Profile')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'My email templates')->exists()) {
           $permission = Permission::create(['name' => 'My email templates']);
           $permission->parent_id = Permission::where('name' ,'Connect')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Debtor clients')->exists()) {
           $permission = Permission::create(['name' => 'Debtor clients']);
           $permission->parent_id = Permission::where('name' ,'Connect')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Management of incidents')->exists()) {
           $permission = Permission::create(['name' => 'Management of incidents']);
           $permission->parent_id = Permission::where('name' ,'Connect')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'My business')->exists()) {
           $permission = Permission::create(['name' => 'My business']);
           $permission->parent_id = Permission::where('name' ,'Settings')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Users')->exists()) {
           $permission = Permission::create(['name' => 'Users']);
           $permission->parent_id = Permission::where('name' ,'Settings')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Roles')->exists()) {
           $permission = Permission::create(['name' => 'Roles']);
           $permission->parent_id = Permission::where('name' ,'Settings')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Bank Accounts')->exists()) {
           $permission = Permission::create(['name' => 'Bank Accounts']);
           $permission->parent_id = Permission::where('name' ,'Settings')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'My templates')->exists()) {
           $permission = Permission::create(['name' => 'My templates']);
           $permission->parent_id = Permission::where('name' ,'Settings')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'References')->exists()) {
           $permission = Permission::create(['name' => 'References']);
           $permission->parent_id = Permission::where('name' ,'Settings')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Payment Terms')->exists()) {
           $permission = Permission::create(['name' => 'Payment Terms']);
           $permission->parent_id = Permission::where('name' ,'Settings')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Price Rates')->exists()) {
           $permission = Permission::create(['name' => 'Price Rates']);
           $permission->parent_id = Permission::where('name' ,'Settings')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Payment Options')->exists()) {
           $permission = Permission::create(['name' => 'Payment Options']);
           $permission->parent_id = Permission::where('name' ,'Settings')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Client and Supplier Categories')->exists()) {
           $permission = Permission::create(['name' => 'Client and Supplier Categories']);
           $permission->parent_id = Permission::where('name' ,'Settings')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Product Categories')->exists()) {
           $permission = Permission::create(['name' => 'Product Categories']);
           $permission->parent_id = Permission::where('name' ,'Settings')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Delivery Options')->exists()) {
           $permission = Permission::create(['name' => 'Delivery Options']);
           $permission->parent_id = Permission::where('name' ,'Settings')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Event Types')->exists()) {
           $permission = Permission::create(['name' => 'Event Types']);
           $permission->parent_id = Permission::where('name' ,'Settings')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Expense Categories')->exists()) {
           $permission = Permission::create(['name' => 'Expense Categories']);
           $permission->parent_id = Permission::where('name' ,'Settings')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Custom states')->exists()) {
           $permission = Permission::create(['name' => 'Custom states']);
           $permission->parent_id = Permission::where('name' ,'Settings')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Email Configuration')->exists()) {
           $permission = Permission::create(['name' => 'Email Configuration']);
           $permission->parent_id = Permission::where('name' ,'Settings')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Taxes')->exists()) {
           $permission = Permission::create(['name' => 'Taxes']);
           $permission->parent_id = Permission::where('name' ,'Settings')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Advanced Settings')->exists()) {
           $permission = Permission::create(['name' => 'Advanced Settings']);
           $permission->parent_id = Permission::where('name' ,'Settings')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Automatic Tasks')->exists()) {
           $permission = Permission::create(['name' => 'Automatic Tasks']);
           $permission->parent_id = Permission::where('name' ,'Settings')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Accounting')->exists()) {
           $permission = Permission::create(['name' => 'Accounting']);
           $permission->parent_id = Permission::where('name' ,'Settings')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Import from CSV')->exists()) {
           $permission = Permission::create(['name' => 'Import from CSV']);
           $permission->parent_id = Permission::where('name' ,'Settings')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Export to CSV')->exists()) {
           $permission = Permission::create(['name' => 'Export to CSV']);
           $permission->parent_id = Permission::where('name' ,'Settings')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Update from CSV')->exists()) {
           $permission = Permission::create(['name' => 'Update from CSV']);
           $permission->parent_id = Permission::where('name' ,'Settings')->pluck('id')->first();
           $permission->is_checkbox = 0;
           $permission->save();
        }

        if (!Permission::where('name', 'Show pricing')->exists()) {
           $permission = Permission::create(['name' => 'Show pricing']);
           $permission->parent_id = Permission::where('name' ,'Other Configuration')->pluck('id')->first();
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Show purchase pricing')->exists()) {
           $permission = Permission::create(['name' => 'Show purchase pricing']);
           $permission->parent_id = Permission::where('name' ,'Other Configuration')->pluck('id')->first();
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Show margin')->exists()) {
           $permission = Permission::create(['name' => 'Show margin']);
           $permission->parent_id = Permission::where('name' ,'Other Configuration')->pluck('id')->first();
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'Show stock')->exists()) {
           $permission = Permission::create(['name' => 'Show stock']);
           $permission->parent_id = Permission::where('name' ,'Other Configuration')->pluck('id')->first();
           $permission->is_checkbox = 1;
           $permission->save();
        }

        /* products */

        if (!Permission::where('name', 'view products')->exists()) {
           $permission = Permission::create(['name' => 'view products']);
           $permission->parent_id = Permission::where('name' ,'Products')->pluck('id')->first();
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'edit products')->exists()) {
           $permission = Permission::create(['name' => 'edit products']);
           $permission->parent_id = Permission::where('name' ,'Products')->pluck('id')->first();
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'create products')->exists()) {
           $permission = Permission::create(['name' => 'create products']);
           $permission->parent_id = Permission::where('name' ,'Products')->pluck('id')->first();
           $permission->is_checkbox = 1;
           $permission->save();
        }

        if (!Permission::where('name', 'delete products')->exists()) {
           $permission = Permission::create(['name' => 'delete products']);
           $permission->parent_id = Permission::where('name' ,'Products')->pluck('id')->first();
           $permission->is_checkbox = 1;
           $permission->save();
        }

        /* services */

        if (!Permission::where('name', 'view services')->exists()) {
            $permission = Permission::create(['name' => 'view services']);
            $permission->parent_id = Permission::where('name' ,'Services')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit services')->exists()) {
            $permission = Permission::create(['name' => 'edit services']);
            $permission->parent_id = Permission::where('name' ,'Services')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create services')->exists()) {
            $permission = Permission::create(['name' => 'create services']);
            $permission->parent_id = Permission::where('name' ,'Services')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete services')->exists()) {
            $permission = Permission::create(['name' => 'delete services']);
            $permission->parent_id = Permission::where('name' ,'Services')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* expenses and investments */

        if (!Permission::where('name', 'view expenses and investments')->exists()) {
            $permission = Permission::create(['name' => 'view expenses and investments']);
            $permission->parent_id = Permission::where('name' ,'Expenses & Investments')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit expenses and investments')->exists()) {
            $permission = Permission::create(['name' => 'edit expenses and investments']);
            $permission->parent_id = Permission::where('name' ,'Expenses & Investments')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create expenses and investments')->exists()) {
            $permission = Permission::create(['name' => 'create expenses and investments']);
            $permission->parent_id = Permission::where('name' ,'Expenses & Investments')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete expenses and investments')->exists()) {
            $permission = Permission::create(['name' => 'delete expenses and investments']);
            $permission->parent_id = Permission::where('name' ,'Expenses & Investments')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* client assets */

        if (!Permission::where('name', 'view client assets')->exists()) {
            $permission = Permission::create(['name' => 'view client assets']);
            $permission->parent_id = Permission::where('name' ,'Client Assets')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit client assets')->exists()) {
            $permission = Permission::create(['name' => 'edit client assets']);
            $permission->parent_id = Permission::where('name' ,'Client Assets')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create client assets')->exists()) {
           
            $permission = Permission::create(['name' => 'create client assets']);
            $permission->parent_id = Permission::where('name' ,'Client Assets')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete client assets')->exists()) {
            
            $permission = Permission::create(['name' => 'delete client assets']);
            $permission->parent_id = Permission::where('name' ,'Client Assets')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* clients */

        if (!Permission::where('name', 'view clients')->exists()) {
           
            $permission = Permission::create(['name' => 'view clients']);
            $permission->parent_id = Permission::where('name' ,'Client ')->where('parent_id', '!=', 0)->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit clients')->exists()) {
           
            $permission = Permission::create(['name' => 'edit clients']);
            $permission->parent_id = Permission::where('name' ,'Client')->where('parent_id', '!=', 0)->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create clients')->exists()) {

            $permission = Permission::create(['name' => 'create clients']);
            $permission->parent_id = Permission::where('name' ,'Client')->where('parent_id', '!=', 0)->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete clients')->exists()) {
           
            $permission = Permission::create(['name' => 'delete clients']);
            $permission->parent_id = Permission::where('name' ,'Client')->where('parent_id', '!=', 0)->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* clients */

        if (!Permission::where('name', 'view potential clients')->exists()) {
            $permission = Permission::create(['name' => 'view potential clients']);
            $permission->parent_id = Permission::where('name' ,'Potential Clients')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit potential clients')->exists()) {
           
            $permission = Permission::create(['name' => 'edit potential clients']);
            $permission->parent_id = Permission::where('name' ,'Potential Clients')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create potential clients')->exists()) {
           
            $permission = Permission::create(['name' => 'create potential clients']);
            $permission->parent_id = Permission::where('name' ,'Potential Clients')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete potential clients')->exists()) {
           
            $permission = Permission::create(['name' => 'delete potential clients']);
            $permission->parent_id = Permission::where('name' ,'Potential Clients')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* contacts */

        if (!Permission::where('name', 'view contacts')->exists()) {
            
            $permission = Permission::create(['name' => 'view contacts']);
            $permission->parent_id = Permission::where('name' ,'Contacts')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit contacts')->exists()) {
           
            $permission = Permission::create(['name' => 'edit contacts']);
            $permission->parent_id = Permission::where('name' ,'Contacts')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create contacts')->exists()) {
            
            $permission = Permission::create(['name' => 'create contacts']);
            $permission->parent_id = Permission::where('name' ,'Contacts')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete contacts')->exists()) {
            
            $permission = Permission::create(['name' => 'delete contacts']);
            $permission->parent_id = Permission::where('name' ,'Contacts')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* client bank account */

        if (!Permission::where('name', 'view client bank account')->exists()) {
           
            $permission = Permission::create(['name' => 'view client bank account']);
            $permission->parent_id = Permission::where('name' ,'Client bank account')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit client bank account')->exists()) {
            
            $permission = Permission::create(['name' => 'edit client bank account']);
            $permission->parent_id = Permission::where('name' ,'Client bank account')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* sales */

        if (!Permission::where('name', 'view estimates')->exists()) {
           
            $permission = Permission::create(['name' => 'view estimates']);
            $permission->parent_id = Permission::where('name' ,'Estimates')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit estimates')->exists()) {
           
            $permission = Permission::create(['name' => 'edit estimates']);
            $permission->parent_id = Permission::where('name' ,'Estimates')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create estimates')->exists()) {
           
            $permission = Permission::create(['name' => 'create estimates']);
            $permission->parent_id = Permission::where('name' ,'Estimates')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete estimates')->exists()) {
           
            $permission = Permission::create(['name' => 'delete estimates']);
            $permission->parent_id = Permission::where('name' ,'Estimates')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* orders */

        if (!Permission::where('name', 'view orders')->exists()) {
           
            $permission = Permission::create(['name' => 'view orders']);
            $permission->parent_id = Permission::where('name' ,'Orders')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit orders')->exists()) {
           
            $permission = Permission::create(['name' => 'edit orders']);
            $permission->parent_id = Permission::where('name' ,'Orders')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create orders')->exists()) {
           
            $permission = Permission::create(['name' => 'create orders']);
            $permission->parent_id = Permission::where('name' ,'Orders')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete orders')->exists()) {
            
            $permission = Permission::create(['name' => 'delete orders']);
            $permission->parent_id = Permission::where('name' ,'Orders')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* delivery notes */

        if (!Permission::where('name', 'view delivery notes')->exists()) {
           
            $permission = Permission::create(['name' => 'view delivery notes']);
            $permission->parent_id = Permission::where('name' ,'Delivery Notes')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit delivery notes')->exists()) {
           
            $permission = Permission::create(['name' => 'edit delivery notes']);
            $permission->parent_id = Permission::where('name' ,'Delivery Notes')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create delivery notes')->exists()) {
           
            $permission = Permission::create(['name' => 'create delivery notes']);
            $permission->parent_id = Permission::where('name' ,'Delivery Notes')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete delivery notes')->exists()) {
           
            $permission = Permission::create(['name' => 'delete delivery notes']);
            $permission->parent_id = Permission::where('name' ,'Delivery Notes')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* Incidents */

        if (!Permission::where('name', 'view incidents')->exists()) {
           
            $permission = Permission::create(['name' => 'view incidents']);
            $permission->parent_id = Permission::where('name' ,'Incidents')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit incidents')->exists()) {
           
            $permission = Permission::create(['name' => 'edit incidents']);
            $permission->parent_id = Permission::where('name' ,'Incidents')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create incidents')->exists()) {
           
            $permission = Permission::create(['name' => 'create incidents']);
            $permission->parent_id = Permission::where('name' ,'Incidents')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete incidents')->exists()) {
           
            $permission = Permission::create(['name' => 'delete incidents']);
            $permission->parent_id = Permission::where('name' ,'Incidents')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* Work Estimate */

        if (!Permission::where('name', 'view work estimate')->exists()) {
           
            $permission = Permission::create(['name' => 'view work estimate']);
            $permission->parent_id = Permission::where('name' ,'Work Estimate')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit work estimate')->exists()) {
           
            $permission = Permission::create(['name' => 'edit work estimate']);
            $permission->parent_id = Permission::where('name' ,'Work Estimate')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create work estimate')->exists()) {
          
            $permission =  Permission::create(['name' => 'create work estimate']);
            $permission->parent_id = Permission::where('name' ,'Work Estimate')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete work estimate')->exists()) {
           
            $permission =  Permission::create(['name' => 'delete work estimate']);
            $permission->parent_id = Permission::where('name' ,'Work Estimate')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* Work Orders */

        if (!Permission::where('name', 'view work orders')->exists()) {
           
            $permission =  Permission::create(['name' => 'view work orders']);
            $permission->parent_id = Permission::where('name' ,'Work Orders')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit work orders')->exists()) {
           
            $permission =  Permission::create(['name' => 'edit work orders']);
            $permission->parent_id = Permission::where('name' ,'Work Orders')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create work orders')->exists()) {
           
            $permission =  Permission::create(['name' => 'create work orders']);
            $permission->parent_id = Permission::where('name' ,'Work Orders')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();

        }

        if (!Permission::where('name', 'delete work orders')->exists()) {
           
            $permission =  Permission::create(['name' => 'delete work orders']);
            $permission->parent_id = Permission::where('name' ,'Work Orders')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* Work delivery notes */

        if (!Permission::where('name', 'view work delivery notes')->exists()) {
           
            $permission =  Permission::create(['name' => 'view work delivery notes']);
            $permission->parent_id = Permission::where('name' ,'Work Delivery Notes')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit work delivery notes')->exists()) {
           
            $permission =  Permission::create(['name' => 'edit work delivery notes']);
            $permission->parent_id = Permission::where('name' ,'Work Delivery Notes')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create work delivery notes')->exists()) {
           
            $permission =  Permission::create(['name' => 'create work delivery notes']);
            $permission->parent_id = Permission::where('name' ,'Work Delivery Notes')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete work delivery notes')->exists()) {
           
            $permission =  Permission::create(['name' => 'delete work delivery notes']);
            $permission->parent_id = Permission::where('name' ,'Work Delivery Notes')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* Invoices */

        if (!Permission::where('name', 'view invoices')->exists()) {
           
            $permission =  Permission::create(['name' => 'view invoices']);
            $permission->parent_id = Permission::where('name' ,'Invoices')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit invoices')->exists()) {
           
            $permission =  Permission::create(['name' => 'edit invoices']);
            $permission->parent_id = Permission::where('name' ,'Invoices')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create invoices')->exists()) {
            
            $permission =  Permission::create(['name' => 'create invoices']);
            $permission->parent_id = Permission::where('name' ,'Invoices')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete invoices')->exists()) {
           
            $permission =  Permission::create(['name' => 'delete invoices']);
            $permission->parent_id = Permission::where('name' ,'Invoices')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* Refunds */

        if (!Permission::where('name', 'view refunds')->exists()) {
           
            $permission =  Permission::create(['name' => 'view refunds']);
            $permission->parent_id = Permission::where('name' ,'Refunds')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit refunds')->exists()) {
           
            $permission =  Permission::create(['name' => 'edit refunds']);
            $permission->parent_id = Permission::where('name' ,'Refunds')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create refunds')->exists()) {
           
            $permission =  Permission::create(['name' => 'create refunds']);
            $permission->parent_id = Permission::where('name' ,'Refunds')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete refunds')->exists()) {
           
            $permission =  Permission::create(['name' => 'delete refunds']);
            $permission->parent_id = Permission::where('name' ,'Refunds')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* Ordinary Invoice Receipts */

        if (!Permission::where('name', 'view ordinary invoice receipts')->exists()) {
            
            $permission = Permission::create(['name' => 'view ordinary invoice receipts']);
            $permission->parent_id = Permission::where('name' ,'Ordinary Invoice Receipts')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit ordinary invoice receipts')->exists()) {
           
            $permission = Permission::create(['name' => 'edit ordinary invoice receipts']);
            $permission->parent_id = Permission::where('name' ,'Ordinary Invoice Receipts')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* Refund Receipts */

        if (!Permission::where('name', 'view refund receipts')->exists()) {
           
            $permission = Permission::create(['name' => 'view refund receipts']);
            $permission->parent_id = Permission::where('name' ,'Refund Receipts')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit refund receipts')->exists()) {
           
            $permission = Permission::create(['name' => 'edit refund receipts']);
            $permission->parent_id = Permission::where('name' ,'Refund Receipts')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* Invoice Summary */

        if (!Permission::where('name', 'view invoice summary')->exists()) {
           
            $permission = Permission::create(['name' => 'view invoice summary']);
            $permission->parent_id = Permission::where('name' ,'Invoice Summary')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* Tickets and other expenses */

        if (!Permission::where('name', 'view tickets and expenses')->exists()) {
           
            $permission = Permission::create(['name' => 'view tickets and expenses']);
            $permission->parent_id = Permission::where('name' ,'Tickets and other expenses')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit tickets and expenses')->exists()) {
           
            $permission = Permission::create(['name' => 'edit tickets and expenses']);
            $permission->parent_id = Permission::where('name' ,'Tickets and other expenses')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create tickets and expenses')->exists()) {
           
            $permission = Permission::create(['name' => 'create tickets and expenses']);
            $permission->parent_id = Permission::where('name' ,'Tickets and other expenses')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete tickets and expenses')->exists()) {
           
            $permission = Permission::create(['name' => 'delete tickets and expenses']);
            $permission->parent_id = Permission::where('name' ,'Tickets and other expenses')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* Purchase Orders */

        if (!Permission::where('name', 'view purchase orders')->exists()) {
           
            $permission = Permission::create(['name' => 'view purchase orders']);
            $permission->parent_id = Permission::where('name' ,'Purchase Orders')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit purchase orders')->exists()) {
           
            $permission = Permission::create(['name' => 'edit purchase orders']);
            $permission->parent_id = Permission::where('name' ,'Purchase Orders')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create purchase orders')->exists()) {
           
            $permission = Permission::create(['name' => 'create purchase orders']);
            $permission->parent_id = Permission::where('name' ,'Purchase Orders')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete purchase orders')->exists()) {
           
            $permission = Permission::create(['name' => 'delete purchase orders']);
            $permission->parent_id = Permission::where('name' ,'Purchase Orders')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* Purchase Delivery Notes */

        if (!Permission::where('name', 'view purchase delivery notes')->exists()) {
           
            $permission = Permission::create(['name' => 'view purchase delivery notes']);
            $permission->parent_id = Permission::where('name' ,'Purchase Delivery Notes')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit purchase delivery notes')->exists()) {
           
            $permission = Permission::create(['name' => 'edit purchase delivery notes']);
            $permission->parent_id = Permission::where('name' ,'Purchase Delivery Notes')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create purchase delivery notes')->exists()) {
           
            $permission = Permission::create(['name' => 'create purchase delivery notes']);
            $permission->parent_id = Permission::where('name' ,'Purchase Delivery Notes')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete purchase delivery notes')->exists()) {
           
            $permission = Permission::create(['name' => 'delete purchase delivery notes']);
            $permission->parent_id = Permission::where('name' ,'Purchase Delivery Notes')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* Purchase Invoices */

        if (!Permission::where('name', 'view purchase invoices')->exists()) {
           
            $permission = Permission::create(['name' => 'view purchase invoices']);
            $permission->parent_id = Permission::where('name' ,'Purchase Invoices')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit purchase invoices')->exists()) {
           
            $permission = Permission::create(['name' => 'edit purchase invoices']);
            $permission->parent_id = Permission::where('name' ,'Purchase Invoices')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create purchase invoices')->exists()) {
           
            $permission = Permission::create(['name' => 'create purchase invoices']);
            $permission->parent_id = Permission::where('name' ,'Purchase Invoices')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete purchase invoices')->exists()) {
           
            $permission = Permission::create(['name' => 'delete purchase invoices']);
            $permission->parent_id = Permission::where('name' ,'Purchase Invoices')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* Purchase Invoice Receipts */

        if (!Permission::where('name', 'view purchase invoice receipts')->exists()) {
           
            $permission = Permission::create(['name' => 'view purchase invoice receipts']);
            $permission->parent_id = Permission::where('name' ,'Purchase Invoice Receipts')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit purchase invoice receipts')->exists()) {
           
            $permission = Permission::create(['name' => 'edit purchase invoice receipts']);
            $permission->parent_id = Permission::where('name' ,'Purchase Invoice Receipts')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* Purchase Invoice Summary */

        if (!Permission::where('name', 'view purchase invoice summary')->exists()) {
           
            $permission = Permission::create(['name' => 'view purchase invoice summary']);
            $permission->parent_id = Permission::where('name' ,'Purchase Invoice Summary')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* Suppliers */

        if (!Permission::where('name', 'view suppliers')->exists()) {
           
            $permission = Permission::create(['name' => 'view suppliers']);
            $permission->parent_id = Permission::where('name' ,'Suppliers')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit suppliers')->exists()) {
           
            $permission = Permission::create(['name' => 'edit suppliers']);
            $permission->parent_id = Permission::where('name' ,'Suppliers')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create suppliers')->exists()) {
           
            $permission = Permission::create(['name' => 'create suppliers']);
            $permission->parent_id = Permission::where('name' ,'Suppliers')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete suppliers')->exists()) {
           
            $permission = Permission::create(['name' => 'delete suppliers']);
            $permission->parent_id = Permission::where('name' ,'Suppliers')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* Suppliers Bank Account  */

        if (!Permission::where('name', 'view suppliers bank account')->exists()) {
           
            $permission = Permission::create(['name' => 'view suppliers bank account']);
            $permission->parent_id = Permission::where('name' ,'Supplier Bank Account')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit suppliers bank account')->exists()) {
           
            $permission = Permission::create(['name' => 'edit suppliers bank account']);
            $permission->parent_id = Permission::where('name' ,'Supplier Bank Account')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }



        /* Profile */
        if (!Permission::where('name', 'view document')->exists()) {
           
            $permission = Permission::create(['name' => 'view document']);
            $permission->parent_id = Permission::where('name' ,'Document Management')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit document')->exists()) {
           
            $permission = Permission::create(['name' => 'edit document']);
            $permission->parent_id = Permission::where('name' ,'Document Management')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* Connect email templates*/

        if (!Permission::where('name', 'view email templates')->exists()) {
           
            $permission = Permission::create(['name' => 'view email templates']);
            $permission->parent_id = Permission::where('name' ,'My email templates')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit email templates')->exists()) {
           
            $permission = Permission::create(['name' => 'edit email templates']);
            $permission->parent_id = Permission::where('name' ,'My email templates')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create email templates')->exists()) {
           
            $permission = Permission::create(['name' => 'create email templates']);
            $permission->parent_id = Permission::where('name' ,'My email templates')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete email templates')->exists()) {
           
            $permission = Permission::create(['name' => 'delete email templates']);
            $permission->parent_id = Permission::where('name' ,'My email templates')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'send email templates')->exists()) {
           
            $permission = Permission::create(['name' => 'send email templates']);
            $permission->parent_id = Permission::where('name' ,'My email templates')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* Connect Debtor clients  */


        if (!Permission::where('name', 'edit debtor clients')->exists()) {
           
            $permission = Permission::create(['name' => 'edit debtor clients']);
            $permission->parent_id = Permission::where('name' ,'Debtor clients')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'send debtor clients')->exists()) {
           
            $permission = Permission::create(['name' => 'send debtor clients']);
            $permission->parent_id = Permission::where('name' ,'Debtor clients')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* Management of incidents */

        if (!Permission::where('name', 'edit management of incidents')->exists()) {
           
            $permission = Permission::create(['name' => 'edit management of incidents']);
            $permission->parent_id = Permission::where('name' ,'Management of incidents')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* Settings business */

        if (!Permission::where('name', 'view my business')->exists()) {
           
            $permission = Permission::create(['name' => 'view my business']);
            $permission->parent_id = Permission::where('name' ,'My business')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();

        }

        if (!Permission::where('name', 'edit my business')->exists()) {
           
            $permission = Permission::create(['name' => 'edit my business']);
            $permission->parent_id = Permission::where('name' ,'My business')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* users */

        if (!Permission::where('name', 'view users')->exists()) {
           
            $permission = Permission::create(['name' => 'view users']);
            $permission->parent_id = Permission::where('name' ,'Users')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit users')->exists()) {
           
            $permission = Permission::create(['name' => 'edit users']);
            $permission->parent_id = Permission::where('name' ,'Users')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create users')->exists()) {
           
            $permission = Permission::create(['name' => 'create users']);
            $permission->parent_id = Permission::where('name' ,'Users')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete users')->exists()) {
           
            $permission = Permission::create(['name' => 'delete users']);
            $permission->parent_id = Permission::where('name' ,'Users')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* roles */

        if (!Permission::where('name', 'view roles')->exists()) {
           
            $permission = Permission::create(['name' => 'view roles']);
            $permission->parent_id = Permission::where('name' ,'Roles')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit roles')->exists()) {
           
            $permission = Permission::create(['name' => 'edit roles']);
            $permission->parent_id = Permission::where('name' ,'Roles')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create roles')->exists()) {
           
            $permission = Permission::create(['name' => 'create roles']);
            $permission->parent_id = Permission::where('name' ,'Roles')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete roles')->exists()) {
           
            $permission = Permission::create(['name' => 'delete roles']);
            $permission->parent_id = Permission::where('name' ,'Roles')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* bank accounts */

        if (!Permission::where('name', 'view bank accounts')->exists()) {
           
            $permission = Permission::create(['name' => 'view bank accounts']);
            $permission->parent_id = Permission::where('name' ,'Bank Accounts')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit bank accounts')->exists()) {
           
            $permission = Permission::create(['name' => 'edit bank accounts']);
            $permission->parent_id = Permission::where('name' ,'Bank Accounts')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create bank accounts')->exists()) {
           
            $permission = Permission::create(['name' => 'create bank accounts']);
            $permission->parent_id = Permission::where('name' ,'Bank Accounts')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete bank accounts')->exists()) {
           
            $permission = Permission::create(['name' => 'delete bank accounts']);
            $permission->parent_id = Permission::where('name' ,'Bank Accounts')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* My templates */

        if (!Permission::where('name', 'view my templates')->exists()) {
           
            $permission = Permission::create(['name' => 'view my templates']);
            $permission->parent_id = Permission::where('name' ,'My templates')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit my templates')->exists()) {
           
            $permission = Permission::create(['name' => 'edit my templates']);
            $permission->parent_id = Permission::where('name' ,'My templates')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create my templates')->exists()) {
           
            $permission = Permission::create(['name' => 'create my templates']);
            $permission->parent_id = Permission::where('name' ,'My templates')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete my templates')->exists()) {
           
            $permission = Permission::create(['name' => 'delete my templates']);
            $permission->parent_id = Permission::where('name' ,'My templates')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* References */

        if (!Permission::where('name', 'view references')->exists()) {
           
            $permission = Permission::create(['name' => 'view references']);
            $permission->parent_id = Permission::where('name' ,'References')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit references')->exists()) {
           
            $permission = Permission::create(['name' => 'edit references']);
            $permission->parent_id = Permission::where('name' ,'References')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create references')->exists()) {
           
            $permission = Permission::create(['name' => 'create references']);
            $permission->parent_id = Permission::where('name' ,'References')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete references')->exists()) {
           
            $permission = Permission::create(['name' => 'delete references']);
            $permission->parent_id = Permission::where('name' ,'References')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* Payment terms  */

        if (!Permission::where('name', 'view payment terms')->exists()) {
           
            $permission = Permission::create(['name' => 'view payment terms']);
            $permission->parent_id = Permission::where('name' ,'Payment Terms')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit payment terms')->exists()) {
           
            $permission = Permission::create(['name' => 'edit payment terms']);
            $permission->parent_id = Permission::where('name' ,'Payment Terms')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create payment terms')->exists()) {
           
            $permission = Permission::create(['name' => 'create payment terms']);
            $permission->parent_id = Permission::where('name' ,'Payment Terms')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete payment terms')->exists()) {
           
            $permission = Permission::create(['name' => 'delete payment terms']);
            $permission->parent_id = Permission::where('name' ,'Payment Terms')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();

        }

        /* Price Rates  */

        if (!Permission::where('name', 'view price rates')->exists()) {
           
            $permission = Permission::create(['name' => 'view price rates']);
            $permission->parent_id = Permission::where('name' ,'Price Rates')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit price rates')->exists()) {
           
            $permission = Permission::create(['name' => 'edit price rates']);
            $permission->parent_id = Permission::where('name' ,'Price Rates')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create price rates')->exists()) {
           
            $permission = Permission::create(['name' => 'create price rates']);
            $permission->parent_id = Permission::where('name' ,'Price Rates')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete price rates')->exists()) {
           
            $permission = Permission::create(['name' => 'delete price rates']);
            $permission->parent_id = Permission::where('name' ,'Price Rates')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* Payment Options  */

        if (!Permission::where('name', 'view payment options')->exists()) {
           
            $permission = Permission::create(['name' => 'view payment options']);
            $permission->parent_id = Permission::where('name' ,'Payment Options')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit payment options')->exists()) {
           
            $permission = Permission::create(['name' => 'edit payment options']);
            $permission->parent_id = Permission::where('name' ,'Payment Options')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create payment options')->exists()) {
           
            $permission = Permission::create(['name' => 'create payment options']);
            $permission->parent_id = Permission::where('name' ,'Payment Options')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete payment options')->exists()) {
           
            $permission = Permission::create(['name' => 'delete payment options']);
            $permission->parent_id = Permission::where('name' ,'Payment Options')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /*  Client and Supplier Categories  */

        if (!Permission::where('name', 'view client and supplier categories')->exists()) {
           
            $permission = Permission::create(['name' => 'view client and supplier categories']);
            $permission->parent_id = Permission::where('name' ,'Client and Supplier Categories')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit client and supplier categories')->exists()) {
           
            $permission = Permission::create(['name' => 'edit client and supplier categories']);
            $permission->parent_id = Permission::where('name' ,'Client and Supplier Categories')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create client and supplier categories')->exists()) {
           
            $permission = Permission::create(['name' => 'create client and supplier categories']);
            $permission->parent_id = Permission::where('name' ,'Client and Supplier Categories')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete client and supplier categories')->exists()) {
           
            $permission = Permission::create(['name' => 'delete client and supplier categories']);
            $permission->parent_id = Permission::where('name' ,'Client and Supplier Categories')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /*  Product Categories  */

        if (!Permission::where('name', 'view product categories')->exists()) {
           
            $permission = Permission::create(['name' => 'view product categories']);
            $permission->parent_id = Permission::where('name' ,'Product Categories')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit product categories')->exists()) {
           
            $permission = Permission::create(['name' => 'edit product categories']);
            $permission->parent_id = Permission::where('name' ,'Product Categories')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create product categories')->exists()) {
           
            $permission = Permission::create(['name' => 'create product categories']);
            $permission->parent_id = Permission::where('name' ,'Product Categories')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete product categories')->exists()) {
           
            $permission = Permission::create(['name' => 'delete product categories']);
            $permission->parent_id = Permission::where('name' ,'Product Categories')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /*  Delivery Options */

        if (!Permission::where('name', 'view delivery options')->exists()) {
           
            $permission = Permission::create(['name' => 'view delivery options']);
            $permission->parent_id = Permission::where('name' ,'Delivery Options')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit delivery options')->exists()) {
           
            $permission = Permission::create(['name' => 'edit delivery options']);
            $permission->parent_id = Permission::where('name' ,'Delivery Options')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create delivery options')->exists()) {
           
            $permission = Permission::create(['name' => 'create delivery options']);
            $permission->parent_id = Permission::where('name' ,'Delivery Options')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete delivery options')->exists()) {
           
            $permission = Permission::create(['name' => 'delete delivery options']);
            $permission->parent_id = Permission::where('name' ,'Delivery Options')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /*  Event Types  */

        if (!Permission::where('name', 'view event types')->exists()) {
           
            $permission = Permission::create(['name' => 'view event types']);
            $permission->parent_id = Permission::where('name' ,'Event Types')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit event types')->exists()) {
           
            $permission = Permission::create(['name' => 'edit event types']);
            $permission->parent_id = Permission::where('name' ,'Event Types')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create event types')->exists()) {
           
            $permission = Permission::create(['name' => 'create event types']);
            $permission->parent_id = Permission::where('name' ,'Event Types')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete event types')->exists()) {
           
            $permission = Permission::create(['name' => 'delete event types']);
            $permission->parent_id = Permission::where('name' ,'Event Types')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /*  Expense categories  */

        if (!Permission::where('name', 'view expense categories')->exists()) {
           
            $permission = Permission::create(['name' => 'view expense categories']);
            $permission->parent_id = Permission::where('name' ,'Expense Categories')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit expense categories')->exists()) {
           
            $permission = Permission::create(['name' => 'edit expense categories']);
            $permission->parent_id = Permission::where('name' ,'Expense Categories')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create expense categories')->exists()) {
           
            $permission = Permission::create(['name' => 'create expense categories']);
            $permission->parent_id = Permission::where('name' ,'Expense Categories')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete expense categories')->exists()) {
           
            $permission = Permission::create(['name' => 'delete expense categories']);
            $permission->parent_id = Permission::where('name' ,'Expense Categories')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /*  Custom States  */

        if (!Permission::where('name', 'view custom states')->exists()) {
           
            $permission = Permission::create(['name' => 'view custom states']);
            $permission->parent_id = Permission::where('name' ,'Custom states')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit custom states')->exists()) {
           
            $permission = Permission::create(['name' => 'edit custom states']);
            $permission->parent_id = Permission::where('name' ,'Custom states')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /*  Email Configuration  */

        if (!Permission::where('name', 'view email configuration')->exists()) {
           
            $permission = Permission::create(['name' => 'view email configuration']);
            $permission->parent_id = Permission::where('name' ,'Email Configuration')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit email configuration')->exists()) {
           
            $permission = Permission::create(['name' => 'edit email configuration']);
            $permission->parent_id = Permission::where('name' ,'Email Configuration')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /*  Taxes  */

        if (!Permission::where('name', 'view taxes')->exists()) {
           
            $permission = Permission::create(['name' => 'view taxes']);
            $permission->parent_id = Permission::where('name' ,'Taxes')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit taxes')->exists()) {
           
            $permission = Permission::create(['name' => 'edit taxes']);
            $permission->parent_id = Permission::where('name' ,'Taxes')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create taxes')->exists()) {
           
            $permission = Permission::create(['name' => 'create taxes']);
            $permission->parent_id = Permission::where('name' ,'Taxes')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete taxes')->exists()) {
           
            $permission = Permission::create(['name' => 'delete taxes']);
            $permission->parent_id = Permission::where('name' ,'Taxes')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /*  Advance Settings  */

        if (!Permission::where('name', 'view advance settings')->exists()) {
           
            $permission = Permission::create(['name' => 'view advance settings']);
            $permission->parent_id = Permission::where('name' ,'Advanced Settings')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit advance settings')->exists()) {
           
            $permission = Permission::create(['name' => 'edit advance settings']);
            $permission->parent_id = Permission::where('name' ,'Advanced Settings')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /*  Automatic tasks  */

        if (!Permission::where('name', 'view automatic tasks')->exists()) {
           
            $permission = Permission::create(['name' => 'view automatic tasks']);
            $permission->parent_id = Permission::where('name' ,'Automatic Tasks')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'edit automatic tasks')->exists()) {
           
            $permission = Permission::create(['name' => 'edit automatic tasks']);
            $permission->parent_id = Permission::where('name' ,'Automatic Tasks')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'create automatic tasks')->exists()) {
           
            $permission = Permission::create(['name' => 'create automatic tasks']);
            $permission->parent_id = Permission::where('name' ,'Automatic Tasks')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'delete automatic tasks')->exists()) {
           
            $permission = Permission::create(['name' => 'delete automatic tasks']);
            $permission->parent_id = Permission::where('name' ,'Automatic Tasks')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* Import from CSV */
        if (!Permission::where('name', 'Import from CSV')->exists()) {
           
            $permission = Permission::create(['name' => 'Import from CSV']);
            $permission->parent_id = Permission::where('name' ,'Import from CSV')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* Export to CSV */
        if (!Permission::where('name', 'Export to CSV')->exists()) {
           
            $permission = Permission::create(['name' => 'Export to CSV']);
            $permission->parent_id = Permission::where('name' ,'Export to CSV')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* Update from CSV */
        if (!Permission::where('name', 'Update from CSV')->exists()) {
           
            $permission = Permission::create(['name' => 'Update from CSV']);
            $permission->parent_id = Permission::where('name' ,'Update from CSV')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }



        /* Home */
        if (!Permission::where('name', 'Total amounts')->exists()) {
           
            $permission = Permission::create(['name' => 'Total amounts']);
            $permission->parent_id = Permission::where('name' ,'Home')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* Access */

        if (!Permission::where('name', 'Web access')->exists()) {
           
            $permission = Permission::create(['name' => 'Web access']);
            $permission->parent_id = Permission::where('name' ,'Access')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        if (!Permission::where('name', 'Android and IOS access')->exists()) {
           
            $permission = Permission::create(['name' => 'Android and IOS access']);
            $permission->parent_id = Permission::where('name' ,'Access')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        /* Permissions */
        if (!Permission::where('name', 'All permissions')->exists()) {
           
            $permission = Permission::create(['name' => 'All permissions']);
            $permission->parent_id = Permission::where('name' ,'Permissions')->pluck('id')->first();
            $permission->is_checkbox = 1;
            $permission->save();
        }

        $permissions_table_name = "company_".$company_id."_permissions";
        \DB::table($permissions_table_name)->update([
            'guard_name' => 'api'
        ]);

        $roles_table = 'roles';
        Role::setGlobalTable($roles_table);

        // Assign permissions to role
        $roles = Role::all();
        $permissions = Permission::all();
        $table_name = "company_".$company_id."_role_has_permissions";

        foreach ($roles as $role) {
            $company_role_id = \DB::table("company_".$company_id."_roles")->where('name', $role->name)->pluck('id')->first();

            $main_role_id = Role::where('name', $role->name)->pluck('id')->first();

            $role_permissions = \DB::table('role_has_permissions')->where('role_id', $main_role_id)->get()->toArray();
            
            foreach ($role_permissions as $role_permission) {
                \DB::table($table_name)->insert([
                    'permission_id' => $role_permission->permission_id,
                    'role_id' => $company_role_id,
                ]);
            }
        }
    }
}