<?php
namespace App\Helpers;

use App\Models\Company;
use App\Models\CustomStateType;
use App\Models\CustomState;
use App\Models\MyTemplate;
use App\Models\MyTemplateMeta;
use App\Models\DefaultPdfSendOption;
use App\Models\Setting;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\Api\UserController;



use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableHelper
{
    public static function createTables($company_id)
    {
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
                $table->longText('description')->nullable();
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
                    /* logo */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "logo_heading",
                        "option_value" => "Logo"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "logo_show",
                        "option_value" => "1"
                    ]);

                    /* legal name */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "legal_name_heading",
                        "option_value" => "Legal Name"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "legal_name_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "legal_name_text",
                        "option_value" => ""
                    ]);

                    /* name */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "name_heading",
                        "option_value" => "Name"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "name_show",
                        "option_value" => "1"
                    ]);

                    /* TIN */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "tin_heading",
                        "option_value" => "TIN"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "tin_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "tin_text",
                        "option_value" => ""
                    ]);

                    /* Phone */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "phone_heading",
                        "option_value" => "Phone"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "phone_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "phone_text",
                        "option_value" => ""
                    ]);

                    /* Fax */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "fax_heading",
                        "option_value" => "Fax"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "fax_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "fax_text",
                        "option_value" => ""
                    ]);

                    /* Email */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "email_heading",
                        "option_value" => "Email"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "email_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "email_text",
                        "option_value" => ""
                    ]);

                    /* Website */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "website_heading",
                        "option_value" => "Website"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "website_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "website_text",
                        "option_value" => ""
                    ]);

                    /* Address */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "address_heading",
                        "option_value" => "Address"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "address_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "address_text",
                        "option_value" => ""
                    ]);

                    /* Zip/Postal Code */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "zip_code_heading",
                        "option_value" => "Zip/Postal Code"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "zip_code_show",
                        "option_value" => "1"
                    ]);

                    /* City/Town */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "city_heading",
                        "option_value" => "City/Town Code"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "city_show",
                        "option_value" => "1"
                    ]);

                    /* State/Province */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "state_heading",
                        "option_value" => "State/Province"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "state_show",
                        "option_value" => "1"
                    ]);

                    /* Country */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "country_heading",
                        "option_value" => "Country"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "country_show",
                        "option_value" => "1"
                    ]);

                /* company information ends here*/


                /* document information starts here */
                    /* Document Type */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_type_heading",
                        "option_value" => "Document Type"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_type_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_type_text",
                        "option_value" => ""
                    ]);

                    /* Document Title */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_title_heading",
                        "option_value" => "Document Title"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_title_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_title_text",
                        "option_value" => ""
                    ]);

                    /* Section Title */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_section_title_heading",
                        "option_value" => "Section Title"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_section_title_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_section_title_text",
                        "option_value" => $template." INFO"
                    ]);

                    /* Reference */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_reference_heading",
                        "option_value" => "Reference"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_reference_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_reference_text",
                        "option_value" => "Number:"
                    ]);

                    /* Generated From */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_generated_from_heading",
                        "option_value" => "Generated From"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_generated_from_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_generated_from_text",
                        "option_value" => "Generated From:"
                    ]);

                    /* Date */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_date_heading",
                        "option_value" => "Date"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_date_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_date_text",
                        "option_value" => "Date:"
                    ]);

                    /* Payment Option */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_payment_option_heading",
                        "option_value" => "Payment Option"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_payment_option_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_payment_option_text",
                        "option_value" => "Payment Option:"
                    ]);

                    /* Bank Account */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_bank_account_heading",
                        "option_value" => "Bank Account"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_bank_account_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_bank_account_text",
                        "option_value" => "Account:"
                    ]);

                    /* BIC/SWIFT */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_bic_heading",
                        "option_value" => "BIC/SWIFT"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_bic_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_bic_text",
                        "option_value" => "BIC:"
                    ]);

                    /* Status */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_status_heading",
                        "option_value" => "Status"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_status_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_status_text",
                        "option_value" => "Status:"
                    ]);

                    /* Created By */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_created_by_heading",
                        "option_value" => "Created by"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_created_by_show",
                        "option_value" => "0"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_created_by_text",
                        "option_value" => "Created by:"
                    ]);

                    /* Agent */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_agent_heading",
                        "option_value" => "Agent"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_agent_show",
                        "option_value" => "0"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_agent_text",
                        "option_value" => "Agent:"
                    ]);

                    /* Purchase Document Ref. */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "purchase_document_ref_heading",
                        "option_value" => "Purchase Document Ref."
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "purchase_document_ref_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "purchase_document_ref_text",
                        "option_value" => "Purchase Document Ref.:"
                    ]);

                    /* Sent Date: */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_sent_date_heading",
                        "option_value" => "Sent Date"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_sent_date_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_sent_date_text",
                        "option_value" => "Delivery Date:"
                    ]);

                    /* Delivery Option: */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_delivery_option_heading",
                        "option_value" => "Delivery Option"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_delivery_option_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "document_delivery_option_text",
                        "option_value" => "Delivery Option:"
                    ]);

                /* document information ends here*/

                /* Client/Supplier Information starts here */
                    /* Section Title: */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_section_title_heading",
                        "option_value" => "Section Title"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_section_title_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_section_title_text",
                        "option_value" => "CLIENT INFO"
                    ]);

                    /* Reference */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_reference_heading",
                        "option_value" => "Reference"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_reference_show",
                        "option_value" => "0"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_section_title_text",
                        "option_value" => ""
                    ]);

                    /* Legal Name */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_legal_name_heading",
                        "option_value" => "Reference"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_legal_name_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_legal_name_text",
                        "option_value" => ""
                    ]);

                    /* Name */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_name_heading",
                        "option_value" => "Name"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_name_show",
                        "option_value" => "1"
                    ]);

                    /* TIN */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_tin_heading",
                        "option_value" => "TIN"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_tin_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_tin_text",
                        "option_value" => ""
                    ]);

                    /* Phone */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_phone_heading",
                        "option_value" => "Phone"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_phone_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_phone_text",
                        "option_value" => "Phone:"
                    ]);

                    /* Fax */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_fax_heading",
                        "option_value" => "Phone"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_fax_show",
                        "option_value" => "0"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_fax_text",
                        "option_value" => "Fax:"
                    ]);

                    /* Email */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_email_heading",
                        "option_value" => "Email"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_email_show",
                        "option_value" => "0"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_email_text",
                        "option_value" => ""
                    ]);

                    /* Website */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_website_heading",
                        "option_value" => "Email"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_website_show",
                        "option_value" => "0"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_website_text",
                        "option_value" => ""
                    ]);

                    /* Billing Address */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_billing_address_heading",
                        "option_value" => "Billing Address"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_billing_address_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_billing_address_text",
                        "option_value" => ""
                    ]);

                    /* Zip/Postal Code */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_zip_code_heading",
                        "option_value" => "Zip/Postal Code"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_zip_code_show",
                        "option_value" => "1"
                    ]);

                    /* City/Town */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_city_heading",
                        "option_value" => "City/Town Code"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_city_show",
                        "option_value" => "1"
                    ]);

                    /* State/Province */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_state_heading",
                        "option_value" => "State/Province"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_state_show",
                        "option_value" => "1"
                    ]);

                    /* Country */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_country_heading",
                        "option_value" => "Country"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "client_country_show",
                        "option_value" => "1"
                    ]);
                    
                /* Client/Supplier Information ends here */

                /* Items starts here */
                    /* Reference column */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_reference_column_heading",
                        "option_value" => "Reference Col."
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_reference_column_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_reference_column_text",
                        "option_value" => "REF."
                    ]);

                    /* Barcode */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_barcode_heading",
                        "option_value" => "Barcode"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_barcode_show",
                        "option_value" => "1"
                    ]);

                    /* Name column */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_name_column_heading",
                        "option_value" => "Name Col."
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_name_column_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_name_column_text",
                        "option_value" => "NAME"
                    ]);

                    /* Description */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_description_heading",
                        "option_value" => "Description"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_description_show",
                        "option_value" => "1"
                    ]);

                    /* Unit Price column */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_unit_price_column_heading",
                        "option_value" => "Unit Price Col."
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_unit_price_column_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_unit_price_column_text",
                        "option_value" => "PRICE"
                    ]);


                    /* Discount column */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_discount_column_heading",
                        "option_value" => "Discount Col."
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_discount_column_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_discount_column_text",
                        "option_value" => "DISC."
                    ]);

                    /* Units column */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_units_column_heading",
                        "option_value" => "Units Col."
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_units_column_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_units_column_text",
                        "option_value" => "QTY."
                    ]);

                    /* Price column */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_price_column_heading",
                        "option_value" => "Price Col."
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_price_column_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_price_column_text",
                        "option_value" => "SUBTOTAL"
                    ]);

                    /* Tax column */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_tax_column_heading",
                        "option_value" => "Tax Col."
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_tax_column_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_tax_column_text",
                        "option_value" => "TAXES"
                    ]);

                    /* Discount text */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_discount_heading",
                        "option_value" => "Discount text"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_discount_text",
                        "option_value" => "Disc.:"
                    ]);

                    /* Subtotal text */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_subtotal_text_heading",
                        "option_value" => "Subtotal text"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_subtotal_text_text",
                        "option_value" => "Subtotal:"
                    ]);

                    /* Discount line */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_discount_line_heading",
                        "option_value" => "Discount line"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "items_discount_line_text",
                        "option_value" => "Discount on subtotal:"
                    ]);

                /* Items ends here */

                /* Signature and Summary starts here */
                    /* Signature title */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "sign_signature_title_heading",
                        "option_value" => "Signature Title"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "sign_signature_title_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "sign_signature_title_text",
                        "option_value" => "Signed:"
                    ]);

                    /* Signature Name */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "sign_signature_name_heading",
                        "option_value" => "Signature Name"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "sign_signature_name_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "sign_signature_name_text",
                        "option_value" => "Name:"
                    ]);

                    /* TIN Signature */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "sign_tin_signature_heading",
                        "option_value" => "Signature Name"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "sign_tin_signature_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "sign_tin_signature_text",
                        "option_value" => "TIN:"
                    ]);

                    /* Base Text */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "sign_base_text_heading",
                        "option_value" => "Base text"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "sign_base_text_text",
                        "option_value" => "BASE"
                    ]);

                    /* Total Text */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "sign_base_text_heading",
                        "option_value" => "Total text"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "sign_base_text_text",
                        "option_value" => "TOTAL"
                    ]);


                /* Signature and Summary ends here */

                /* Footer and Legal Note starts here */
                    /* Footer */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "footer_heading",
                        "option_value" => "Footer"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "footer_show",
                        "option_value" => "0"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "footer_text",
                        "option_value" => ""
                    ]);

                    /* Legal Note */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "legal_note_heading",
                        "option_value" => "Legal Note"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "legal_note_show",
                        "option_value" => "Down"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "legal_note_text",
                        "option_value" => ""
                    ]);

                /* Footer and Legal Note ends here */

                /* Comments and Addendums starts here */
                    /* Comments */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "comments_heading",
                        "option_value" => "Comments"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "comments_show",
                        "option_value" => "1"
                    ]);

                    /* Comments Title */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "comments_title_heading",
                        "option_value" => "Comments"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "comments_title_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "comments_title_text",
                        "option_value" => "COMMENTS"
                    ]);

                    /* Addendum */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "addendum_heading",
                        "option_value" => "Addendum"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "addendum_show",
                        "option_value" => "1"
                    ]);

                    /* Addendum Title*/
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "addendum_title_heading",
                        "option_value" => "Addendum"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "addendum_title_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "addendum_title_text",
                        "option_value" => "ADDENDUM"
                    ]);

                    /* Addendum as image*/
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "addendum_as_image_heading",
                        "option_value" => "Addendum"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "addendum_as_image_show",
                        "option_value" => "1"
                    ]);


                /* Comments and Addendums ends here */

                /* Client Assets starts here */
                if(in_array($template, ['Ordinary Invoice', 'Refund Invoice', 'Work Delivery Note', 'Work Estimate', 'Work Order'])){
                        /* Section Title */
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_section_title_heading",
                            "option_value" => "Section Title"
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_section_title_show",
                            "option_value" => "1"
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_section_title_text",
                            "option_value" => "ASSETS INFO"
                        ]);

                        /* Image */
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_image_heading",
                            "option_value" => "Image"
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_image_show",
                            "option_value" => "1"
                        ]);

                        /* Reference */
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_reference_heading",
                            "option_value" => "Reference"
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_reference_show",
                            "option_value" => "0"
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_reference_text",
                            "option_value" => ""
                        ]);

                        /* Name */
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_name_heading",
                            "option_value" => "Name"
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_name_show",
                            "option_value" => "1"
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_name_text",
                            "option_value" => "NAME:"
                        ]);

                        /* Identifier */
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_identifier_heading",
                            "option_value" => "Identifier"
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_identifier_show",
                            "option_value" => "1"
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_identifier_text",
                            "option_value" => "Identifier:"
                        ]);


                        /* Serial Number */
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_serial_no_heading",
                            "option_value" => "Serial Number"
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_serial_no_show",
                            "option_value" => "1"
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_serial_no_text",
                            "option_value" => "Serial Number:"
                        ]);

                        /* Brand */
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_brand_heading",
                            "option_value" => "Brand"
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_brand_show",
                            "option_value" => "1"
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_brand_text",
                            "option_value" => "Brand:"
                        ]);

                        /* Model */
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_model_heading",
                            "option_value" => "Brand"
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_brand_show",
                            "option_value" => "1"
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_brand_text",
                            "option_value" => "Brand:"
                        ]);

                        /* Start of the Warranty */
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_start_warranty_heading",
                            "option_value" => "Start of the Warranty"
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_start_warranty_show",
                            "option_value" => "0"
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_start_warranty_text",
                            "option_value" => ""
                        ]);

                        /* End of the Warranty */
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_end_warranty_heading",
                            "option_value" => "End of the Warranty"
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_end_warranty_show",
                            "option_value" => "0"
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_end_warranty_text",
                            "option_value" => ""
                        ]);

                        /* Description */
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_description_heading",
                            "option_value" => "End of the Warranty"
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_description_show",
                            "option_value" => "0"
                        ]);
                        MyTemplateMeta::create([
                            "template_id" => $template_created->id,
                            "option_name" => "client_assets_end_description_text",
                            "option_value" => ""
                        ]);
                        
                        
                    /* Client Assets ends here */
                }

                if(in_array($template, ['Ordinary Invoice', 'Purchase Invoice', 'Refund Invoice'])){
                    /* Payment Terms starts here */
                    /* Payment Terms */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "payment_terms_heading",
                        "option_value" => "Payment Terms"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "payment_terms_show",
                        "option_value" => "1"
                    ]);

                    /* Payment Terms Title  */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "payment_terms_title_heading",
                        "option_value" => "Payment Terms"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "payment_terms_title_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "payment_terms_title_text",
                        "option_value" => "Payment Terms:"
                    ]);

                    /* Date Column  */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "payment_terms_date_heading",
                        "option_value" => "Date Col."
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "payment_terms_title_text",
                        "option_value" => "DATE"
                    ]);

                    /* Amount Column  */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "payment_terms_amount_heading",
                        "option_value" => "Amount Col."
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "payment_terms_amount_text",
                        "option_value" => "AMOUNT"
                    ]);

                    /* Paid Column  */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "payment_terms_paid_heading",
                        "option_value" => "Paid Col."
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "payment_terms_paid_show",
                        "option_value" => "1"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "payment_terms_paid_text",
                        "option_value" => "PAID"
                    ]);

                    /* Paid text  */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "payment_terms_paid_text_heading",
                        "option_value" => "Paid text"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "payment_terms_paid_text_text",
                        "option_value" => "Yes"
                    ]);
                   
                    /* Unpaid text  */
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "payment_terms_unpaid_text_heading",
                        "option_value" => "Unpaid text"
                    ]);
                    MyTemplateMeta::create([
                        "template_id" => $template_created->id,
                        "option_name" => "payment_terms_unpaid_text_text",
                        "option_value" => "No"
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

        /* Create roles */

        if (!Role::where('name', 'Super Admin')->exists()) {
           Role::create(['name' => 'Super Admin']);
           Permission::create(['name' => 'Super Admin']);
        }

        if (!Role::where('name', 'Admin')->exists()) {
           Role::create(['name' => 'Admin']);
           Permission::create(['name' => 'Admin']);
        }

        if (!Role::where('name', 'Sales Admin')->exists()) {
           Role::create(['name' => 'Sales Admin']);
           Permission::create(['name' => 'Sales Admin']);
        }

        if (!Role::where('name', 'Salesperson')->exists()) {
           Role::create(['name' => 'Salesperson']);
           Permission::create(['name' => 'Salesperson']);
        }

        if (!Role::where('name', 'Technical Admin')->exists()) {
           Role::create(['name' => 'Technical Admin']);
           Permission::create(['name' => 'Technical Admin']);
        }

        if (!Role::where('name', 'Technician')->exists()) {
           Role::create(['name' => 'Technician']);
           Permission::create(['name' => 'Technician']);
        }

        /* Create Permissions */

        /* products */

        if (!Permission::where('name', 'view products')->exists()) {
           Permission::create(['name' => 'view products']);
        }

        if (!Permission::where('name', 'edit products')->exists()) {
           Permission::create(['name' => 'edit products']);
        }

        if (!Permission::where('name', 'create products')->exists()) {
           Permission::create(['name' => 'create products']);
        }

        if (!Permission::where('name', 'delete products')->exists()) {
           Permission::create(['name' => 'delete products']);
        }

        /* services */

        if (!Permission::where('name', 'view services')->exists()) {
           Permission::create(['name' => 'view services']);
        }

        if (!Permission::where('name', 'edit services')->exists()) {
           Permission::create(['name' => 'edit services']);
        }

        if (!Permission::where('name', 'create services')->exists()) {
           Permission::create(['name' => 'create services']);
        }

        if (!Permission::where('name', 'delete services')->exists()) {
           Permission::create(['name' => 'delete services']);
        }

        /* expenses and investments */

        if (!Permission::where('name', 'view expenses and investments')->exists()) {
           Permission::create(['name' => 'view expenses and investments']);
        }

        if (!Permission::where('name', 'edit expenses and investments')->exists()) {
           Permission::create(['name' => 'edit expenses and investments']);
        }

        if (!Permission::where('name', 'create expenses and investments')->exists()) {
           Permission::create(['name' => 'create expenses and investments']);
        }

        if (!Permission::where('name', 'delete expenses and investments')->exists()) {
           Permission::create(['name' => 'delete expenses and investments']);
        }

        /* client assets */

        if (!Permission::where('name', 'view client assets')->exists()) {
           Permission::create(['name' => 'view client assets']);
        }

        if (!Permission::where('name', 'edit client assets')->exists()) {
           Permission::create(['name' => 'edit client assets']);
        }

        if (!Permission::where('name', 'create client assets')->exists()) {
           Permission::create(['name' => 'create client assets']);
        }

        if (!Permission::where('name', 'delete client assets')->exists()) {
           Permission::create(['name' => 'delete client assets']);
        }

        /* clients */

        if (!Permission::where('name', 'view clients')->exists()) {
           Permission::create(['name' => 'view clients']);
        }

        if (!Permission::where('name', 'edit clients')->exists()) {
           Permission::create(['name' => 'edit clients']);
        }

        if (!Permission::where('name', 'create clients')->exists()) {
           Permission::create(['name' => 'create clients']);
        }

        if (!Permission::where('name', 'delete clients')->exists()) {
           Permission::create(['name' => 'delete clients']);
        }

        /* clients */

        if (!Permission::where('name', 'view potential clients')->exists()) {
           Permission::create(['name' => 'view potential clients']);
        }

        if (!Permission::where('name', 'edit potential clients')->exists()) {
           Permission::create(['name' => 'edit potential clients']);
        }

        if (!Permission::where('name', 'create potential clients')->exists()) {
           Permission::create(['name' => 'create potential clients']);
        }

        if (!Permission::where('name', 'delete potential clients')->exists()) {
           Permission::create(['name' => 'delete potential clients']);
        }

        /* contacts */

        if (!Permission::where('name', 'view contacts')->exists()) {
           Permission::create(['name' => 'view contacts']);
        }

        if (!Permission::where('name', 'edit contacts')->exists()) {
           Permission::create(['name' => 'edit contacts']);
        }

        if (!Permission::where('name', 'create contacts')->exists()) {
           Permission::create(['name' => 'create contacts']);
        }

        if (!Permission::where('name', 'delete contacts')->exists()) {
           Permission::create(['name' => 'delete contacts']);
        }

        /* client bank account */

        if (!Permission::where('name', 'view client bank account')->exists()) {
           Permission::create(['name' => 'view client bank account']);
        }

        if (!Permission::where('name', 'edit client bank account')->exists()) {
           Permission::create(['name' => 'edit client bank account']);
        }

        /* sales */

        if (!Permission::where('name', 'view estimates')->exists()) {
           Permission::create(['name' => 'view estimates']);
        }

        if (!Permission::where('name', 'edit estimates')->exists()) {
           Permission::create(['name' => 'edit estimates']);
        }

        if (!Permission::where('name', 'create estimates')->exists()) {
           Permission::create(['name' => 'create estimates']);
        }

        if (!Permission::where('name', 'delete estimates')->exists()) {
           Permission::create(['name' => 'delete estimates']);
        }

        /* orders */

        if (!Permission::where('name', 'view orders')->exists()) {
           Permission::create(['name' => 'view orders']);
        }

        if (!Permission::where('name', 'edit orders')->exists()) {
           Permission::create(['name' => 'edit orders']);
        }

        if (!Permission::where('name', 'create orders')->exists()) {
           Permission::create(['name' => 'create orders']);
        }

        if (!Permission::where('name', 'delete orders')->exists()) {
           Permission::create(['name' => 'delete orders']);
        }

        /* delivery notes */

        if (!Permission::where('name', 'view delivery notes')->exists()) {
           Permission::create(['name' => 'view delivery notes']);
        }

        if (!Permission::where('name', 'edit delivery notes')->exists()) {
           Permission::create(['name' => 'edit delivery notes']);
        }

        if (!Permission::where('name', 'create delivery notes')->exists()) {
           Permission::create(['name' => 'create delivery notes']);
        }

        if (!Permission::where('name', 'delete delivery notes')->exists()) {
           Permission::create(['name' => 'delete delivery notes']);
        }

        /* Incidents */

        if (!Permission::where('name', 'view incidents')->exists()) {
           Permission::create(['name' => 'view incidents']);
        }

        if (!Permission::where('name', 'edit incidents')->exists()) {
           Permission::create(['name' => 'edit incidents']);
        }

        if (!Permission::where('name', 'create incidents')->exists()) {
           Permission::create(['name' => 'create incidents']);
        }

        if (!Permission::where('name', 'delete incidents')->exists()) {
           Permission::create(['name' => 'delete incidents']);
        }

        /* Work Estimate */

        if (!Permission::where('name', 'view work estimate')->exists()) {
           Permission::create(['name' => 'view work estimate']);
        }

        if (!Permission::where('name', 'edit work estimate')->exists()) {
           Permission::create(['name' => 'edit work estimate']);
        }

        if (!Permission::where('name', 'create work estimate')->exists()) {
           Permission::create(['name' => 'create work estimate']);
        }

        if (!Permission::where('name', 'delete work estimate')->exists()) {
           Permission::create(['name' => 'delete work estimate']);
        }

        /* Work Orders */

        if (!Permission::where('name', 'view work orders')->exists()) {
           Permission::create(['name' => 'view work orders']);
        }

        if (!Permission::where('name', 'edit work orders')->exists()) {
           Permission::create(['name' => 'edit work orders']);
        }

        if (!Permission::where('name', 'create work orders')->exists()) {
           Permission::create(['name' => 'create work orders']);
        }

        if (!Permission::where('name', 'delete work orders')->exists()) {
           Permission::create(['name' => 'delete work orders']);
        }

        /* Work delivery notes */

        if (!Permission::where('name', 'view work delivery notes')->exists()) {
           Permission::create(['name' => 'view work delivery notes']);
        }

        if (!Permission::where('name', 'edit work delivery notes')->exists()) {
           Permission::create(['name' => 'edit work delivery notes']);
        }

        if (!Permission::where('name', 'create work delivery notes')->exists()) {
           Permission::create(['name' => 'create work delivery notes']);
        }

        if (!Permission::where('name', 'delete work delivery notes')->exists()) {
           Permission::create(['name' => 'delete work delivery notes']);
        }

        /* Invoices */

        if (!Permission::where('name', 'view invoices')->exists()) {
           Permission::create(['name' => 'view invoices']);
        }

        if (!Permission::where('name', 'edit invoices')->exists()) {
           Permission::create(['name' => 'edit invoices']);
        }

        if (!Permission::where('name', 'create invoices')->exists()) {
           Permission::create(['name' => 'create invoices']);
        }

        if (!Permission::where('name', 'delete invoices')->exists()) {
           Permission::create(['name' => 'delete invoices']);
        }

        /* Refunds */

        if (!Permission::where('name', 'view refunds')->exists()) {
           Permission::create(['name' => 'view refunds']);
        }

        if (!Permission::where('name', 'edit refunds')->exists()) {
           Permission::create(['name' => 'edit refunds']);
        }

        if (!Permission::where('name', 'create refunds')->exists()) {
           Permission::create(['name' => 'create refunds']);
        }

        if (!Permission::where('name', 'delete refunds')->exists()) {
           Permission::create(['name' => 'delete refunds']);
        }

        /* Ordinary Invoice Receipts */

        if (!Permission::where('name', 'view ordinary invoice receipts')->exists()) {
           Permission::create(['name' => 'view ordinary invoice receipts']);
        }

        if (!Permission::where('name', 'edit ordinary invoice receipts')->exists()) {
           Permission::create(['name' => 'edit ordinary invoice receipts']);
        }

        /* Refund Receipts */

        if (!Permission::where('name', 'view refund receipts')->exists()) {
           Permission::create(['name' => 'view refund receipts']);
        }

        if (!Permission::where('name', 'edit refund receipts')->exists()) {
           Permission::create(['name' => 'edit refund receipts']);
        }

        /* Invoice Summary */

        if (!Permission::where('name', 'view invoice summary')->exists()) {
           Permission::create(['name' => 'view invoice summary']);
        }

        /* Tickets and other expenses */

        if (!Permission::where('name', 'view tickets and expenses')->exists()) {
           Permission::create(['name' => 'view tickets and expenses']);
        }

        if (!Permission::where('name', 'edit tickets and expenses')->exists()) {
           Permission::create(['name' => 'edit tickets and expenses']);
        }

        if (!Permission::where('name', 'create tickets and expenses')->exists()) {
           Permission::create(['name' => 'create tickets and expenses']);
        }

        if (!Permission::where('name', 'delete tickets and expenses')->exists()) {
           Permission::create(['name' => 'delete tickets and expenses']);
        }

        /* Purchase Orders */

        if (!Permission::where('name', 'view purchase orders')->exists()) {
           Permission::create(['name' => 'view purchase orders']);
        }

        if (!Permission::where('name', 'edit purchase orders')->exists()) {
           Permission::create(['name' => 'edit purchase orders']);
        }

        if (!Permission::where('name', 'create purchase orders')->exists()) {
           Permission::create(['name' => 'create purchase orders']);
        }

        if (!Permission::where('name', 'delete purchase orders')->exists()) {
           Permission::create(['name' => 'delete purchase orders']);
        }

        /* Purchase Delivery Notes */

        if (!Permission::where('name', 'view purchase delivery notes')->exists()) {
           Permission::create(['name' => 'view purchase delivery notes']);
        }

        if (!Permission::where('name', 'edit purchase delivery notes')->exists()) {
           Permission::create(['name' => 'edit purchase delivery notes']);
        }

        if (!Permission::where('name', 'create purchase delivery notes')->exists()) {
           Permission::create(['name' => 'create purchase delivery notes']);
        }

        if (!Permission::where('name', 'delete purchase delivery notes')->exists()) {
           Permission::create(['name' => 'delete purchase delivery notes']);
        }

        /* Purchase Invoices */

        if (!Permission::where('name', 'view purchase invoices')->exists()) {
           Permission::create(['name' => 'view purchase invoices']);
        }

        if (!Permission::where('name', 'edit purchase invoices')->exists()) {
           Permission::create(['name' => 'edit purchase invoices']);
        }

        if (!Permission::where('name', 'create purchase invoices')->exists()) {
           Permission::create(['name' => 'create purchase invoices']);
        }

        if (!Permission::where('name', 'delete purchase invoices')->exists()) {
           Permission::create(['name' => 'delete purchase invoices']);
        }

        /* Purchase Invoice Receipts */

        if (!Permission::where('name', 'view purchase invoice receipts')->exists()) {
           Permission::create(['name' => 'view purchase invoice receipts']);
        }

        if (!Permission::where('name', 'edit purchase invoice receipts')->exists()) {
           Permission::create(['name' => 'edit purchase invoice receipts']);
        }

        /* Purchase Invoice Summary */

        if (!Permission::where('name', 'view purchase invoice summary')->exists()) {
           Permission::create(['name' => 'view purchase invoice summary']);
        }

        /* Suppliers */

        if (!Permission::where('name', 'view suppliers')->exists()) {
           Permission::create(['name' => 'view suppliers']);
        }

        if (!Permission::where('name', 'edit suppliers')->exists()) {
           Permission::create(['name' => 'edit suppliers']);
        }

        if (!Permission::where('name', 'create suppliers')->exists()) {
           Permission::create(['name' => 'create suppliers']);
        }

        if (!Permission::where('name', 'delete suppliers')->exists()) {
           Permission::create(['name' => 'delete suppliers']);
        }

        /* Suppliers Bank Account  */

        if (!Permission::where('name', 'view suppliers bank account')->exists()) {
           Permission::create(['name' => 'view suppliers bank account']);
        }

        if (!Permission::where('name', 'edit suppliers bank account')->exists()) {
           Permission::create(['name' => 'edit suppliers bank account']);
        }

        /* Calendar */
        if (!Permission::where('name', 'calendar  calendar')->exists()) {
           Permission::create(['name' => 'view calendar']);
        }

        if (!Permission::where('name', 'Show all events in the related section')->exists()) {
           Permission::create(['name' => 'Show all events in the related section']);
        }

        if (!Permission::where('name', 'calendar tasks')->exists()) {
           Permission::create(['name' => 'calendar tasks']);
        }

        /* Reports */
        if (!Permission::where('name', 'Reports overview')->exists()) {
           Permission::create(['name' => 'Reports overview']);
        }

        if (!Permission::where('name', 'Reports invoicing by client')->exists()) {
           Permission::create(['name' => 'Reports invoicing by client']);
        }

        if (!Permission::where('name', 'Reports invoicing by agent')->exists()) {
           Permission::create(['name' => 'Reports invoicing by agent']);
        }

        if (!Permission::where('name', 'Reports invoicing by item')->exists()) {
           Permission::create(['name' => 'Reports invoicing by item']);
        }

        if (!Permission::where('name', 'Reports cashflow overview')->exists()) {
           Permission::create(['name' => 'Reports cashflow overview']);
        }

        if (!Permission::where('name', 'Reports Cash Flow by Payment Options')->exists()) {
           Permission::create(['name' => 'Reports Cash Flow by Payment Options']);
        }

        if (!Permission::where('name', 'Reports Cash Flow by Agent')->exists()) {
           Permission::create(['name' => 'Reports Cash Flow by Agent']);
        }

        if (!Permission::where('name', 'Reports Sales Overview')->exists()) {
           Permission::create(['name' => 'Reports Sales Overview']);
        }

        if (!Permission::where('name', 'Reports Sales by Client')->exists()) {
           Permission::create(['name' => 'Reports Sales by Client']);
        }

        if (!Permission::where('name', 'Reports Sales by Agent')->exists()) {
           Permission::create(['name' => 'Reports Sales by Agent']);
        }

        if (!Permission::where('name', 'Reports Sales by Item')->exists()) {
           Permission::create(['name' => 'Reports Sales by Item']);
        }

        if (!Permission::where('name', 'Reports Technical Service Overview              
')->exists()) {
           Permission::create(['name' => 'Reports Technical Service Overview                
']);
        }

        if (!Permission::where('name', 'Reports Incidents by Client')->exists()) {
           Permission::create(['name' => 'Reports Incidents by Client']);
        }

        if (!Permission::where('name', 'Reports Incidents by Agent')->exists()) {
           Permission::create(['name' => 'Reports Incidents by Agent']);
        }

        if (!Permission::where('name', 'Reports Technical Service by Client')->exists()) {
           Permission::create(['name' => 'Reports Technical Service by Client']);
        }

        if (!Permission::where('name', 'Reports Technical Service by Agent')->exists()) {
           Permission::create(['name' => 'Reports Technical Service by Agent']);
        }

        if (!Permission::where('name', 'Reports Technical Service by Item')->exists()) {
           Permission::create(['name' => 'Reports Technical Service by Item']);
        }

        if (!Permission::where('name', 'Reports Purchases by Provider')->exists()) {
           Permission::create(['name' => 'Reports Purchases by Provider']);
        }

        if (!Permission::where('name', 'Reports Purchases by Item')->exists()) {
           Permission::create(['name' => 'Reports Purchases by Item']);
        }

        if (!Permission::where('name', 'Reports Stock Valuation')->exists()) {
           Permission::create(['name' => 'Reports Stock Valuation']);
        }

        if (!Permission::where('name', 'Reports View tax Reports')->exists()) {
           Permission::create(['name' => 'Reports View tax Reports']);
        }

        /* Profile */
        if (!Permission::where('name', 'view document')->exists()) {
           Permission::create(['name' => 'view document']);
        }

        if (!Permission::where('name', 'edit document')->exists()) {
           Permission::create(['name' => 'edit document']);
        }

        /* Connect email templates*/

        if (!Permission::where('name', 'view email templates')->exists()) {
           Permission::create(['name' => 'view email templates']);
        }

        if (!Permission::where('name', 'edit email templates')->exists()) {
           Permission::create(['name' => 'edit email templates']);
        }

        if (!Permission::where('name', 'create email templates')->exists()) {
           Permission::create(['name' => 'create email templates']);
        }

        if (!Permission::where('name', 'delete email templates')->exists()) {
           Permission::create(['name' => 'delete email templates']);
        }

        if (!Permission::where('name', 'send email templates')->exists()) {
           Permission::create(['name' => 'send email templates']);
        }

        /* Connect Debtor clients  */


        if (!Permission::where('name', 'edit debtor clients')->exists()) {
           Permission::create(['name' => 'edit debtor clients']);
        }

        if (!Permission::where('name', 'send debtor clients')->exists()) {
           Permission::create(['name' => 'send debtor clients']);
        }

        /* Management of incidents */

        if (!Permission::where('name', 'edit management of incidents')->exists()) {
           Permission::create(['name' => 'edit management of incidents']);
        }

        /* Settings business */

        if (!Permission::where('name', 'view my business')->exists()) {
           Permission::create(['name' => 'view my business']);
        }

        if (!Permission::where('name', 'edit my business')->exists()) {
           Permission::create(['name' => 'edit my business']);
        }

        /* users */

        if (!Permission::where('name', 'view users')->exists()) {
           Permission::create(['name' => 'view users']);
        }

        if (!Permission::where('name', 'edit users')->exists()) {
           Permission::create(['name' => 'edit users']);
        }

        if (!Permission::where('name', 'create users')->exists()) {
           Permission::create(['name' => 'create users']);
        }

        if (!Permission::where('name', 'delete users')->exists()) {
           Permission::create(['name' => 'delete users']);
        }

        /* roles */

        if (!Permission::where('name', 'view roles')->exists()) {
           Permission::create(['name' => 'view roles']);
        }

        if (!Permission::where('name', 'edit roles')->exists()) {
           Permission::create(['name' => 'edit roles']);
        }

        if (!Permission::where('name', 'create roles')->exists()) {
           Permission::create(['name' => 'create roles']);
        }

        if (!Permission::where('name', 'delete roles')->exists()) {
           Permission::create(['name' => 'delete roles']);
        }

        /* bank accounts */

        if (!Permission::where('name', 'view bank accounts')->exists()) {
           Permission::create(['name' => 'view bank accounts']);
        }

        if (!Permission::where('name', 'edit bank accounts')->exists()) {
           Permission::create(['name' => 'edit bank accounts']);
        }

        if (!Permission::where('name', 'create bank accounts')->exists()) {
           Permission::create(['name' => 'create bank accounts']);
        }

        if (!Permission::where('name', 'delete bank accounts')->exists()) {
           Permission::create(['name' => 'delete bank accounts']);
        }

        /* My templates */

        if (!Permission::where('name', 'view my templates')->exists()) {
           Permission::create(['name' => 'view my templates']);
        }

        if (!Permission::where('name', 'edit my templates')->exists()) {
           Permission::create(['name' => 'edit my templates']);
        }

        if (!Permission::where('name', 'create my templates')->exists()) {
           Permission::create(['name' => 'create my templates']);
        }

        if (!Permission::where('name', 'delete my templates')->exists()) {
           Permission::create(['name' => 'delete my templates']);
        }

        /* References */

        if (!Permission::where('name', 'view references')->exists()) {
           Permission::create(['name' => 'view references']);
        }

        if (!Permission::where('name', 'edit references')->exists()) {
           Permission::create(['name' => 'edit references']);
        }

        if (!Permission::where('name', 'create references')->exists()) {
           Permission::create(['name' => 'create references']);
        }

        if (!Permission::where('name', 'delete references')->exists()) {
           Permission::create(['name' => 'delete references']);
        }

        /* Payment terms  */

        if (!Permission::where('name', 'view payment terms')->exists()) {
           Permission::create(['name' => 'view payment terms']);
        }

        if (!Permission::where('name', 'edit payment terms')->exists()) {
           Permission::create(['name' => 'edit payment terms']);
        }

        if (!Permission::where('name', 'create payment terms')->exists()) {
           Permission::create(['name' => 'create payment terms']);
        }

        if (!Permission::where('name', 'delete payment terms')->exists()) {
           Permission::create(['name' => 'delete payment terms']);
        }

        /* Price Rates  */

        if (!Permission::where('name', 'view price rates')->exists()) {
           Permission::create(['name' => 'view price rates']);
        }

        if (!Permission::where('name', 'edit price rates')->exists()) {
           Permission::create(['name' => 'edit price rates']);
        }

        if (!Permission::where('name', 'create price rates')->exists()) {
           Permission::create(['name' => 'create price rates']);
        }

        if (!Permission::where('name', 'delete price rates')->exists()) {
           Permission::create(['name' => 'delete price rates']);
        }

        /* Payment Options  */

        if (!Permission::where('name', 'view price options')->exists()) {
           Permission::create(['name' => 'view price options']);
        }

        if (!Permission::where('name', 'edit price options')->exists()) {
           Permission::create(['name' => 'edit price options']);
        }

        if (!Permission::where('name', 'create price options')->exists()) {
           Permission::create(['name' => 'create price options']);
        }

        if (!Permission::where('name', 'delete price options')->exists()) {
           Permission::create(['name' => 'delete price options']);
        }

        /*  Client and Supplier Categories  */

        if (!Permission::where('name', 'view client and supplier categories')->exists()) {
           Permission::create(['name' => 'view client and supplier categories']);
        }

        if (!Permission::where('name', 'edit client and supplier categories')->exists()) {
           Permission::create(['name' => 'edit client and supplier categories']);
        }

        if (!Permission::where('name', 'create client and supplier categories')->exists()) {
           Permission::create(['name' => 'create client and supplier categories']);
        }

        if (!Permission::where('name', 'delete client and supplier categories')->exists()) {
           Permission::create(['name' => 'delete client and supplier categories']);
        }

        /*  Product Categories  */

        if (!Permission::where('name', 'view product categories')->exists()) {
           Permission::create(['name' => 'view product categories']);
        }

        if (!Permission::where('name', 'edit product categories')->exists()) {
           Permission::create(['name' => 'edit product categories']);
        }

        if (!Permission::where('name', 'create product categories')->exists()) {
           Permission::create(['name' => 'create product categories']);
        }

        if (!Permission::where('name', 'delete product categories')->exists()) {
           Permission::create(['name' => 'delete product categories']);
        }

        /*  Delivery Options */

        if (!Permission::where('name', 'view delivery options')->exists()) {
           Permission::create(['name' => 'view delivery options']);
        }

        if (!Permission::where('name', 'edit delivery options')->exists()) {
           Permission::create(['name' => 'edit delivery options']);
        }

        if (!Permission::where('name', 'create delivery options')->exists()) {
           Permission::create(['name' => 'create delivery options']);
        }

        if (!Permission::where('name', 'delete delivery options')->exists()) {
           Permission::create(['name' => 'delete delivery options']);
        }

        /*  Event Types  */

        if (!Permission::where('name', 'view event types')->exists()) {
           Permission::create(['name' => 'view event types']);
        }

        if (!Permission::where('name', 'edit event types')->exists()) {
           Permission::create(['name' => 'edit event types']);
        }

        if (!Permission::where('name', 'create event types')->exists()) {
           Permission::create(['name' => 'create event types']);
        }

        if (!Permission::where('name', 'delete event types')->exists()) {
           Permission::create(['name' => 'delete event types']);
        }

        /*  Expense categories  */

        if (!Permission::where('name', 'view expense categories')->exists()) {
           Permission::create(['name' => 'view expense categories']);
        }

        if (!Permission::where('name', 'edit expense categories')->exists()) {
           Permission::create(['name' => 'edit expense categories']);
        }

        if (!Permission::where('name', 'create expense categories')->exists()) {
           Permission::create(['name' => 'create expense categories']);
        }

        if (!Permission::where('name', 'delete expense categories')->exists()) {
           Permission::create(['name' => 'delete expense categories']);
        }

        /*  Custom States  */

        if (!Permission::where('name', 'view custom states')->exists()) {
           Permission::create(['name' => 'view custom states']);
        }

        if (!Permission::where('name', 'edit custom states')->exists()) {
           Permission::create(['name' => 'edit custom states']);
        }

        /*  Email Configuration  */

        if (!Permission::where('name', 'view email configuration')->exists()) {
           Permission::create(['name' => 'view email configuration']);
        }

        if (!Permission::where('name', 'edit email configuration')->exists()) {
           Permission::create(['name' => 'edit email configuration']);
        }

        /*  Taxes  */

        if (!Permission::where('name', 'view taxes')->exists()) {
           Permission::create(['name' => 'view taxes']);
        }

        if (!Permission::where('name', 'edit taxes')->exists()) {
           Permission::create(['name' => 'edit taxes']);
        }

        if (!Permission::where('name', 'create taxes')->exists()) {
           Permission::create(['name' => 'create taxes']);
        }

        if (!Permission::where('name', 'delete taxes')->exists()) {
           Permission::create(['name' => 'delete taxes']);
        }

        /*  Advance Settings  */

        if (!Permission::where('name', 'view advance settings')->exists()) {
           Permission::create(['name' => 'view advance settings']);
        }

        if (!Permission::where('name', 'edit advance settings')->exists()) {
           Permission::create(['name' => 'edit advance settings']);
        }

        /*  Automatic tasks  */

        if (!Permission::where('name', 'view automatic tasks')->exists()) {
           Permission::create(['name' => 'view automatic tasks']);
        }

        if (!Permission::where('name', 'edit automatic tasks')->exists()) {
           Permission::create(['name' => 'edit automatic tasks']);
        }

        if (!Permission::where('name', 'create automatic tasks')->exists()) {
           Permission::create(['name' => 'create automatic tasks']);
        }

        if (!Permission::where('name', 'delete automatic tasks')->exists()) {
           Permission::create(['name' => 'delete automatic tasks']);
        }

        /* Import from CSV */
        if (!Permission::where('name', 'Import from CSV')->exists()) {
           Permission::create(['name' => 'Import from CSV']);
        }

        /* Export to CSV */
        if (!Permission::where('name', 'Export to CSV')->exists()) {
           Permission::create(['name' => 'Export to CSV']);
        }

        /* Update from CSV */
        if (!Permission::where('name', 'Update from CSV')->exists()) {
           Permission::create(['name' => 'Update from CSV']);
        }


        /* Other Configuration */

        if (!Permission::where('name', 'Show pricing')->exists()) {
           Permission::create(['name' => 'Show pricing']);
        }

        if (!Permission::where('name', 'Show purchase pricing')->exists()) {
           Permission::create(['name' => 'Show purchase pricing']);
        }

        if (!Permission::where('name', 'Show stock')->exists()) {
           Permission::create(['name' => 'Show stock']);
        }

        /* Home */
        if (!Permission::where('name', 'Total amounts')->exists()) {
           Permission::create(['name' => 'Total amounts']);
        }

        /* Access */

        if (!Permission::where('name', 'Web access')->exists()) {
           Permission::create(['name' => 'Web access']);
        }

        if (!Permission::where('name', 'Android and IOS access')->exists()) {
           Permission::create(['name' => 'Android and IOS access']);
        }

        /* Permissions */
        if (!Permission::where('name', 'All permissions')->exists()) {
           Permission::create(['name' => 'All permissions']);
        }

    }
}