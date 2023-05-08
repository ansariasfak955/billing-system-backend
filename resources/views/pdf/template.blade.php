@php
    $company_email_show = [];
    $company_logo_show = 0;
    $company_website_show = [];
    $company_phone_show = [];
    $company_name_show = 0;
    $company_legal_name_show = 0;
    $company_country_show = 0;
    $document_payment_info_show = 0;
    $document_reference_show = 0;
    $client_supplier_tin_show = 0;
    $document_date_show = 0;
    $document_status_show = 0;
    $document_status_text = 'Status:';
    $document_created_by_show = 0;
    $document_created_by_text = 'Created by:';
    $company_company_info_show = 0;
    $document_title_show = 0;
    $hide_signed_box_show = 1;
    $document_title_text = '';
    $font = $template->font;
    $color = $template->color;
@endphp
  
    <style>
        
        .table_heading{
            color: {{ $color }} !important;
        }
        .header_border {
            border-left: 2px solid {{ $color }} !important;
        }

        span.mdaa {
            color: black;
            position: relative;
            width: 100%;
            text-align: right;
        }

        span.mdaa:before {
            content: "-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------";
            width: 100px;
            height: 2px;
            letter-spacing: -1px;
            /* margin: 0 10px 0 0; */
        }
        
    </style>

    {{-- <style type="text/css">
        @font-face {
            font-family: Bookman;
            src: url('/assets/fonts/Bookman.ttf');
        }
        @font-face {
            font-family: test;
            src: url(/assets/fonts/test.ttf);
        }
        @font-face {
            font-family: Bookman;
            src: url('/assets/fonts/Bookman.ttf');
        }
        @font-face {
            font-family: Roboto;
            src: url("/assets/fonts/Roboto.ttf");
        }

    </style> --}}
<body>
    
    
   
    @foreach($template->metas as $meta)
        @if($meta->category == 'Company Information' && $meta->type == 'logo' && $meta->option_name == 'show')
            @php
            $company_logo_show = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Company Information' && $meta->type == 'name' && $meta->option_name == 'show')
            @php
            $company_name_show = $meta->option_value;
            @endphp
        @endif
        @if($meta->category == 'Company Information' && $meta->type == 'legal' && $meta->option_name == 'show')
            @php
            $company_legal_name_show = $meta->option_value;
            @endphp
        @endif
        @if($meta->category == 'Company Information' && $meta->type == 'legal' && $meta->option_name == 'text')
            @php
            $company_legal_name_text = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Company Information' && $meta->type == 'email' && $meta->option_name == 'show')
            @php
            $company_email_show['show'] = $meta->option_value;
            @endphp
        @endif
        @if($meta->category == 'Company Information' && $meta->type == 'email' && $meta->option_name == 'text')
            @php
            $company_email_show['value'] = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Company Information' && $meta->type == 'website' && $meta->option_name == 'show')
            @php
            $company_website_show['show'] = $meta->option_value;
            @endphp
        @endif
        @if($meta->category == 'Company Information' && $meta->type == 'website' && $meta->option_name == 'text')
            @php
            $company_website_show['value'] = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Company Information' && $meta->type == 'phone' && $meta->option_name == 'show')
            @php
            $company_phone_show['show'] = $meta->option_value;
            @endphp
        @endif
        @if($meta->category == 'Company Information' && $meta->type == 'phone' && $meta->option_name == 'text')
            @php
            $company_phone_show['value'] = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Company Information' && $meta->type == 'country' && $meta->option_name == 'show')
            @php
            $company_country_show = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Company Information' && $meta->type == 'hide_company_information' && $meta->option_name == 'show')
            @php
            $company_company_info_show = $meta->option_value;
            @endphp
        @endif

        {{-- Document Information --}}

        @if($meta->category == 'Document Information' && $meta->type == 'reference' && $meta->option_name == 'show')
            @php
            $document_reference_show = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Document Information' && $meta->type == 'reference' && $meta->option_name == 'text')
            @php
            $document_reference_text = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Document Information' && $meta->type == 'date' && $meta->option_name == 'show')
            @php
            $document_date_show = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Document Information' && $meta->type == 'date' && $meta->option_name == 'text')
            @php
            $document_date_text = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Document Information' && $meta->type == 'document_payment' && $meta->option_name == 'show')
            @php
            $document_payment_info_show = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Document Information' && $meta->type == 'document_payment' && $meta->option_name == 'text')
            @php
            $document_payment_info_text = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Document Information' && $meta->type == 'document_status' && $meta->option_name == 'show')
            @php
            $document_status_show = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Document Information' && $meta->type == 'document_status' && $meta->option_name == 'text')
            @php
            $document_status_text = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Document Information' && $meta->type == 'document_created' && $meta->option_name == 'show')
            @php
            $document_created_by_show = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Document Information' && $meta->type == 'document_created' && $meta->option_name == 'text')
            @php
            $document_created_by_text = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Document Information' && $meta->type == 'document_delivery' && $meta->option_name == 'show')
            @php
            $document_delivery_by_show = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Document Information' && $meta->type == 'document_delivery' && $meta->option_name == 'text')
            @php
            $document_delivery_by_text = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Document Information' && $meta->type == 'document_agent' && $meta->option_name == 'show')
            @php
            $document_agent_show = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Document Information' && $meta->type == 'document_agent' && $meta->option_name == 'text')
            @php
            $document_agent_text = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Document Information' && $meta->type == 'document_type' && $meta->option_name == 'show')
            @php
            $document_type_show = $meta->option_value;
            @endphp
        @endif
        @if($meta->category == 'Document Information' && $meta->type == 'hide_signed_box' && $meta->option_name == 'show')
            @php
                $hide_signed_box_show = (int)$meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Document Information' && $meta->type == 'document_title' && $meta->option_name == 'show')
            @php
            $document_title_show = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Document Information' && $meta->type == 'document_title' && $meta->option_name == 'text')
            @php
            $document_title_text = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Comments and Addendums' && $meta->type == 'addendum' && $meta->option_name == 'show')
            @php
            $comments_show = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Comments and Addendums' && $meta->type == 'addendum_title' && $meta->option_name == 'text')
            @php
            $comments_text = $meta->option_value;
            @endphp
        @endif
        @if($meta->category == 'Comments and Addendums' && $meta->type == 'comments_title' && $meta->option_name == 'show')
            @php
            $comments_title_show = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Comments and Addendums' && $meta->type == 'comments_title' && $meta->option_name == 'text')
            @php
            $comments_title_text = $meta->option_value;
            @endphp
        @endif
        
        {{-- Client Information --}}
        @if($meta->category == 'Client/Supplier Information' && $meta->type == 'client_section' && $meta->option_name == 'show')
            @php
            $client_supplier_section_show = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Client/Supplier Information' && $meta->type == 'client_section' && $meta->option_name == 'text')
            @php
            $client_supplier_section = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Client/Supplier Information' && $meta->type == 'client_legal_name' && $meta->option_name == 'show')
            @php
            $client_supplier_legal_name_show = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Client/Supplier Information' && $meta->type == 'client_legal_name' && $meta->option_name == 'text')
            @php
            $client_supplier_legal_name = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Client/Supplier Information' && $meta->type == 'client_tin' && $meta->option_name == 'show')
            @php
            $client_supplier_tin_show = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Client/Supplier Information' && $meta->type == 'client_tin' && $meta->option_name == 'text')
            @php
            $client_supplier_tin_text = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Client/Supplier Information' && $meta->type == 'client_phone' && $meta->option_name == 'show')
            @php
            $client_supplier_phone_show = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Client/Supplier Information' && $meta->type == 'client_phone' && $meta->option_name == 'text')
            @php
            $client_supplier_phone = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Client/Supplier Information' && $meta->type == 'client_name' && $meta->option_name == 'show')
            @php
            $client_supplier_name_show = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Client/Supplier Information' && $meta->type == 'client_name' && $meta->option_name == 'text')
            @php
            $client_supplier_name = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Client/Supplier Information' && $meta->type == 'client_reference' && $meta->option_name == 'show')
            @php
            $client_supplier_reference_show = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Client/Supplier Information' && $meta->type == 'client_reference' && $meta->option_name == 'text')
            @php
            $client_supplier_reference = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Client/Supplier Information' && $meta->type == 'client_fax' && $meta->option_name == 'show')
            @php
            $client_supplier_fax_show = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Client/Supplier Information' && $meta->type == 'client_fax' && $meta->option_name == 'text')
            @php
            $client_supplier_fax = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Client/Supplier Information' && $meta->type == 'client_supplier' && $meta->option_name == 'show')
            @php
            $client_supplier_email_show = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Client/Supplier Information' && $meta->type == 'client_supplier' && $meta->option_name == 'text')
            @php
            $client_supplier_email = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Client/Supplier Information' && $meta->type == 'client_website' && $meta->option_name == 'show')
            @php
            $client_supplier_website_show = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Client/Supplier Information' && $meta->type == 'client_website' && $meta->option_name == 'text')
            @php
            $client_supplier_website = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Client/Supplier Information' && $meta->type == 'client_billing' && $meta->option_name == 'show')
            @php
            $client_supplier_billing_show = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Client/Supplier Information' && $meta->type == 'client_billing' && $meta->option_name == 'text')
            @php
            $client_supplier_billing = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Client/Supplier Information' && $meta->type == 'client_zip_code' && $meta->option_name == 'show')
            @php
            $client_supplier_zip_code_show = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Client/Supplier Information' && $meta->type == 'client_city' && $meta->option_name == 'show')
            @php
            $client_supplier_city_show = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Client/Supplier Information' && $meta->type == 'client_state' && $meta->option_name == 'show')
            @php
            $client_supplier_state_show = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Client/Supplier Information' && $meta->type == 'client_country' && $meta->option_name == 'show')
            @php
            $client_supplier_country_show = $meta->option_value;
            @endphp
        @endif
        @if($meta->category == 'Signature and Summary' && $meta->type == 'sign_signature_title' && $meta->option_name == 'show')
            @php
                $show_signed_title = $meta->option_value;
            @endphp
        @endif
        @if($meta->category == 'Signature and Summary' && $meta->type == 'sign_signature_title' && $meta->option_name == 'text')
            @php
                $signed_box_text = $meta->option_value;
            @endphp
        @endif
        {{-- RUC Text and show hide --}}
        @if($meta->category == 'Signature and Summary' && $meta->type == 'sign_tin_signature' && $meta->option_name == 'show')
            @php
                $show_signed_tin_title = $meta->option_value;
            @endphp
        @endif
        @if($meta->category == 'Signature and Summary' && $meta->type == 'sign_tin_signature' && $meta->option_name == 'text')
            @php
                $signed_tin_text = $meta->option_value;
            @endphp
        @endif
        {{-- Signature Name show hide --}}
        @if($meta->category == 'Signature and Summary' && $meta->type == 'sign_signature_name' && $meta->option_name == 'show')
            @php
                $sign_signature_title = $meta->option_value;
            @endphp
        @endif
        @if($meta->category == 'Signature and Summary' && $meta->type == 'sign_signature_name' && $meta->option_name == 'text')
            @php
                $sign_signature_text = $meta->option_value;
            @endphp
        @endif
        @if($meta->category == 'Footer and Legal Note' && $meta->type == 'footer_pages' && $meta->option_name == 'show')
            @php
                $show_footer_count = $meta->option_value;
            @endphp
        @endif

        <!-- Footer legal note -->

        @if($meta->category == 'Footer and Legal Note' && $meta->type == 'legal_note' && $meta->option_name == 'show')
            @php
                $legal_note_show = $meta->option_value;
            @endphp
        @endif
        @if($meta->category == 'Footer and Legal Note' && $meta->type == 'legal_note' && $meta->option_name == 'text')
            @php
                $legal_note_text = $meta->option_value;
            @endphp
        @endif

        <!-- Items  -->
        @if($meta->category == 'Items' && $meta->type == 'items_reference' && $meta->option_name == 'show')
            @php
                $show_items = $meta->option_value;
            @endphp
        @endif
        @if($meta->category == 'Items' && $meta->type == 'items_reference' && $meta->option_name == 'text')
            @php
                $item_text = $meta->option_value;
            @endphp
        @endif
        @if($meta->category == 'Items' && $meta->type == 'items_description' && $meta->option_name == 'show')
            @php
                $show_items_description = $meta->option_value;
            @endphp
        @endif
        @if($meta->category == 'Items' && $meta->type == 'items_description' && $meta->option_name == 'text')
            @php
                $item_description_text = $meta->option_value;
            @endphp
        @endif
        @if($meta->category == 'Items' && $meta->type == 'items_units' && $meta->option_name == 'show')
            @php
                $show_items_units = $meta->option_value;
            @endphp
        @endif
        @if($meta->category == 'Items' && $meta->type == 'items_units' && $meta->option_name == 'text')
            @php
                $item_units_text = $meta->option_value;
            @endphp
        @endif
        @if($meta->category == 'Items' && $meta->type == 'items_discount' && $meta->option_name == 'show')
            @php
                $discount_show = $meta->option_value;
            @endphp
        @endif
        @if($meta->category == 'Items' && $meta->type == 'items_discount' && $meta->option_name == 'text')
            @php
                $discount_text = $meta->option_value;
            @endphp
        @endif
        @if($meta->category == 'Items' && $meta->type == 'items_unit_price' && $meta->option_name == 'show')
            @php
                $items_unit_price_show = $meta->option_value;
            @endphp
        @endif
        @if($meta->category == 'Items' && $meta->type == 'items_unit_price' && $meta->option_name == 'text')
            @php
                $items_unit_price_text = $meta->option_value;
            @endphp
        @endif
        @if($meta->category == 'Items' && $meta->type == 'items_price' && $meta->option_name == 'show')
            @php
                $items_price_show = $meta->option_value;
            @endphp
        @endif
        @if($meta->category == 'Items' && $meta->type == 'items_price' && $meta->option_name == 'text')
            @php
                $items_price_text = $meta->option_value;
            @endphp
        @endif

    @endforeach

    @if(strpos($template->watermark,"via.placeholder") !== false)
        @php
        $watermark_image = '';
        @endphp
    @else
        @php
        $watermark_image = $template->watermark;
        @endphp
    @endif

    <style>
        th, td {
            vertical-align: top;
        }
    </style>

    <div style="position:relative; font-size: 12px; font-family:{{$font}};">
        <img src="{{ $watermark_image }}" alt="" style="position: absolute; z-index: -1; opacity: 0.3; top:50%; left: 50%; transform: translate(-50%); width: 600px">
        <div style="margin-top: 0px;height: 45px;">
        @if($company_company_info_show != 1)
            <div style="margin-top: 0px;">
                <table style="border-collapse: collapse; width:100%">
                    <tr>
                        <td style="padding: 0; margin: 0;">
                        @if($company_logo_show)
                            <img src="{{ $company->logo }}" alt="" srcset="" style="width: 80px; height: 80px; object-fit: cover;">
                        @endif
                        </td>
                        <td class="header_border" style="width:250px" @if($company_name_show || $company_country_show) @endif>
                            <span style="margin-left: 20px;">{{__('template.company_name')}}</span>
                            <span>{{  @$company->commercial_name }}</span> <br>
                            @if(@$company->address)
                            <div style="margin-left: 20px;">
                                
                                <span >{{__('template.address')}}</span>
                                <span>{{@$company->address}}</span><br>
                            </div>
                            @endif
                            <div style="margin-left: 20px;">
                            <span>{{@$company->pincode}} {{@$company->city}} {{@$company->country}} {{@$company->tin}}</span><br>
                            </div>
                        </td>
                        <td class="header_border" @if(@$company_email_show['show'] || @$company_website_show['show']) style="width: 300px; " @endif>
                            @if(@$company_email_show['show'] ==1)
                                <span style="margin-left: 30px;">{{__('template.email')}}</span> 
                                @if(@$company_email_show['show'] ==1 && @$company_email_show['value'])
                                    {{$company_email_show['value']}}
                                @elseif(@$company_email_show['show'] ==1 && @!$company_email_show['value'])
                                    {{ $company->email}}
                                @endif
                                <br>
                            @endif
                            @if(@$company_website_show['show'] == 1 && @$company_website_show['value'])
                                <span style="margin-left: 30px;">
                                {{ @$company_website_show['value'] ? @$company_website_show['value'] : 'Website'}}</span> @if(@$company_website_show['show'] ==1 && @$company_website_show['value'])
                                    <!-- {{$company_website_show['value']}} -->{{ $company->website}}
                                @elseif(@$company_website_show['show'] ==1 && @!$company_website_show['value'])
                                    {{ $company->website}}
                                @endif
                            @endif
                            <br>
                            @if(@$company_phone_show['show'] && @$company_phone_show['value'])    
                                <span style="margin-left: 30px;">{{__('template.phone')}}</span> @if(@$company_phone_show['show'] ==1 && @$company_phone_show['value'])
                                    {{$company_phone_show['value']}}
                                @elseif(@$company_phone_show['show'] ==1 && @!$company_phone_show['value'])
                                    {{ $company->phone}}
                                @endif
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        @endif
        </div>
        <div style="text-align: center; margin-top: 70px;">
            @if(@$document_type_show == 1)
                    <h2>{{ $template->name }}</h2>
                    @if($document_title_show && $document_title_text)
                        {{ $document_title_text }}
                    @endif
            @endif
        </div>
        <div style="height:400px">
            <div style="margin-top: 20px;font-size: 13px">
                <table style="border-collapse: collapse; width:50%; padding: 10px; float: left;">
                    <th class="table_heading" style=" border-bottom: 1px solid gray;text-align: left;">{{ strtoupper($template->document_type) }} INFO</th>
                    @if($document_reference_show == 1)
                        <tr>
                            <td style="padding: 0; margin: 0;">
                            {{ $document_reference_text ? $document_reference_text : __('template.number')}} <b> INV00001</b>
                            </td>
                        </tr>
                    @endif
                    @if(@$client_supplier_legal_name_show)
                    <tr><td style="padding: 0; margin: 0;">{{__('template.client_name')}} <b>Johnny {{@$client_supplier_legal_name}}</b></td></tr>
                    @endif
                    {{-- @if(@$client_supplier_tin_show)
                    <tr><td style="padding: 0; margin: 0;">Ced/Ruc: <b>54578 {{@$client_supplier_tin}}</b></td></tr>
                    @endif --}}
                    @if($document_date_show == 1)
                        <tr>
                            <td style="padding: 0; margin: 0;">
                            {{ $document_date_text ? $document_date_text : __('template.date')}} <b>{{ date('d F Y') }}</b>
                            </td>
                        </tr>
                    @endif
                    {{-- @if($document_status_show == 1)
                        <tr>
                            <td style="padding: 0; margin: 0;">
                                {{ $document_status_text ? $document_status_text : __('template.status')}}<b> Pending</b>
                            </td>
                        </tr>
                    @endif --}}

                    @if($document_payment_info_show == 1)
                        <tr>
                            <td style="padding: 0; margin: 0;">
                                {{ $document_payment_info_text ? $document_payment_info_text : 'Payment Option:'}}<b> Online Bank Transfer</b>
                            </td>
                        </tr>
                    @endif
                    
                    @if($document_created_by_show == 1)
                        <tr>
                            <td style="padding: 0; margin: 0;">
                                {{ $document_created_by_text ? $document_created_by_text : __('template.created_by')}}<b> Test View Account</b>
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td style="padding: 0; margin: 0;">
                            {{__('template.delivery_to')}} <b> HongKong 9205 Olive Ave., 10977, Spring Valley, NY, United States</b>
                        </td>
                    </tr>

                    @if(@$document_delivery_by_show == 1)
                        <tr>
                            <td style="padding: 0; margin: 0;">
                                {{ $document_delivery_by_text ? $document_delivery_by_text : __('template.delivery_option')}} <b>test delivery A domicilio</b>
                            </td>
                        </tr>
                    @endif

                    @if(@$document_agent_show == 1)
                        <tr>
                            <td style="padding: 0; margin: 0;">
                                {{ $document_agent_text ? $document_agent_text : __('template.agent')}} <b>Test View Account</b>
                            </td>
                        </tr>
                    @endif
                </table>
                <table style="border-collapse: collapse; width:50%; padding: 10px; float: right;">
                        <th style="color: orange; border-bottom: 1px solid gray;text-align: left; height:16px">
                        @if(@$client_supplier_section_show == 1)
                            {{($client_supplier_section) ? $client_supplier_section : ""}}
                        @endif
                        </th>
        
                    @if((@$client_supplier_name_show || @$client_supplier_legal_name_show) && (@$client_supplier_legal_name || @$client_supplier_name))
                        <tr><td style="padding: 0; margin: 0;">{{__('template.name_legal_name')}} <b>{{$client_supplier_legal_name}}({{$client_supplier_name}})</b></td></tr>
                    @endif

                    @if(@$client_supplier_tin_show == 1 && @$client_supplier_tin_text)
                        <tr>
                            <td style="padding: 0; margin: 0;">
                                {{$client_supplier_tin_text ? $client_supplier_tin_text : 'Ced/Ruc:'}} 
                            </td>
                        </tr>
                    @endif

                    @if(@$client_supplier_phone_show && @$client_supplier_phone)
                        <tr><td style="padding: 0; margin: 0;">{{__('template.phone')}} <b>{{$client_supplier_phone}}</b></td></tr>
                    @endif

                    @if(@$client_supplier_reference_show && @$client_supplier_reference)
                        <tr><td style="padding: 0; margin: 0;">{{__('template.reference')}} <b>{{$client_supplier_reference}}</b></td></tr>
                    @endif

                    @if(@$client_supplier_fax_show && @$client_supplier_fax)
                        <tr><td style="padding: 0; margin: 0;">Fax: <b>{{$client_supplier_fax}}</b></td></tr>
                    @endif

                    @if(@$client_supplier_email_show && @$client_supplier_email)
                        <tr><td style="padding: 0; margin: 0;">{{__('template.email')}} <b>{{$client_supplier_email}}</b></td></tr>
                    @endif

                    @if(@$client_supplier_website_show && @$client_supplier_website)
                        <tr><td style="padding: 0; margin: 0;">Website: <b>{{$client_supplier_website}}</b></td></tr>
                    @endif

                    @if(@$client_supplier_billing_show && @$client_supplier_billing)
                        <tr><td style="padding: 0; margin: 0;">Billing: <b>{{$client_supplier_billing}}</b></td></tr>
                    @endif

                    <tr><td style="padding: 0; margin: 0;">
                    @if(@$client_supplier_zip_code_show == 1) 
                        {{-- <b>{{$client_supplier_zip_code_show}}</b> --}}
                        {{__('template.zip_code')}} <b>90001</b>
                    @endif
                        </td>
                    </tr>
                
                    <tr>
                    {{@$client_supplier_city_show}}
                    <td style="padding: 0; margin: 0;">
                    @if(@$client_supplier_city_show == 1)
                        {{-- <b>{{$client_supplier_city_show}}</b>
                    @else --}}
                        {{__('template.city')}} <b>Los Angeles, California</b>
                    @endif
                        </td>
                    </tr>

                    <tr><td style="padding: 0; margin: 0;">
                    @if(@$client_supplier_state_show == 1) 
                        {{-- <b>{{$client_supplier_state_show}}</b>
                    @else --}}
                        {{__('template.state')}} <b>Alaska</b>
                    @endif
                        </td>
                    </tr>

                    <tr><td style="padding: 0; margin: 0;">
                    @if(@$client_supplier_country_show == 1)
                        {{-- <b>{{$client_supplier_country_show}}</b>
                    @else --}}
                        {{__('template.country')}} <b>USA</b>
                    @endif
                        </td>
                    </tr>
                    
                    
                </table>
            </div>
                    <!-- @if($document_reference_show == 1)
                        <tr>
                            <td style="padding: 0; margin: 0;">
                            {{ $document_reference_text ? $document_reference_text : 'Number:'}} <b> INV00001</b>
                            </td>
                        </tr>
                    @endif -->
            <div style="clear: both;"></div>
            <div style="margin-top: 20px;">
                <table style="border-collapse: collapse; width:100%; ">
                    <tr class="table_heading" style=" border-bottom: 1px solid gray;">
                        @if(@$show_items == 1)
                            <th class="table_heading" style=" border-bottom: 1px solid #999; text-align: left;">
                            {{ @$item_text ? @$item_text : 'REF.'}}
                            </th>
                        @endif
                        @if(@$show_items_description == 1)
                            <th class="table_heading" style=" padding: 0 0 5px; border-bottom: 1px solid #999; text-align: left; width: 350px;">
                            {{ @$item_description_text ? @$item_description_text : __('template.description')}}
                            </th>
                        @endif
                        @if($show_items_units == 1)
                            <th class="table_heading" style=" padding: 0 0 5px; border-bottom: 1px solid #999; text-align: right;">
                            {{ @$item_units_text ? @$item_units_text : __('template.qty')}}
                            </th>
                        @endif
                        @if(@$discount_show == 1)
                                <th class="table_heading" style=" padding: 0 0 5px; border-bottom: 1px solid #999; text-align: right;">
                                {{ @$discount_text ? @$discount_text : __('template.disc')}}
                                </th>
                            @endif
                            @if(@$items_unit_price_show == 1)
                                <th class="table_heading" style=" padding: 0 0 5px; border-bottom: 1px solid #999; text-align: right;">
                                {{ @$items_unit_price_text ? @$items_unit_price_text : __('template.price')}}
                                </th>
                            @endif
                            @if(@$items_price_show == 1)
                                    <th class="table_heading" style=" padding: 0 0 5px; border-bottom: 1px solid #999; text-align: right;">
                                    {{ @$items_price_text ? @$items_price_text : __('template.subtotal')}}
                                    </th>
                            @endif
                       {{--  <th class="table_heading" style="padding: 0 0 5px; border-bottom: 1px solid #999; text-align: left;">TAXES</th> --}}
                    </tr>
                    @php
                    $subtotal = 0;
                    @endphp
                    @foreach($products as $product)
                        <tr>
                            <td style="padding: 0 0 5px; margin: 0; border-bottom: 1px solid #999;">
                                <p style="marging: 0; padding: 0">{{ $product->id }}</p>
                                
                                {{-- @if(strpos($product->image,"via.placeholder") !== false)
                                    @php
                                    $image = 'https://dummyimage.com/67x69/dfdfdf/000000.png&text=Not+Found';
                                    @endphp
                                @else
                                    @php
                                    $image = $product->image;
                                    @endphp
                                @endif

                                <img height="45" src="{{ $image }}" alt="" srcset=""> --}}
                            </td>
                            <td style="padding: 0 0 5px; margin: 0; border-bottom: 1px solid #999; width: 350px;">
                                <p style="marging: 0; padding: 0">{{ $product->name }}</p>
                                <span style="color:#666666;">{{ $product->description }}</span>
                            </td>
                            <td style="padding: 0 0 5px; margin: 0; border-bottom: 1px solid #999; text-align: right;">
                                <p style="marging: 0; padding: 0">1</p>
                            </td>
                            <td style="padding: 0 0 5px; margin: 0; border-bottom: 1px solid #999; text-align: right;">
                                <p style="marging: 0; padding: 0">0</p>
                            </td>
                            <td style="padding: 0 0 5px; margin: 0; border-bottom: 1px solid #999; text-align: right;">
                                <p style="marging: 0; padding: 0">{{ $product->price }}</p>
                            </td>
                            <td style="padding: 0 0 5px; margin: 0; border-bottom: 1px solid #999;  text-align: right;">
                                <p style="marging: 0; padding-left: 15px;">{{ $product->price }}</p>
                            </td>
                           {{--  <td style="padding: 0 0 5px; margin: 0; border-bottom: 1px solid #999;">
                                <p style="marging: 0; padding: 0">VAT 21%</p>
                            </td> --}}
                            @php
                            $subtotal += $product->price;
                            @endphp
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
            @php
            $vat = $subtotal*21/100;
            $total = $vat+$subtotal;
            @endphp

            
        
        @if(@$comments_show == 1)
            <div style="margin-top: 20px;">
                <h5 style="border-bottom: 1px solid black ;">{{ $comments_text }}</h5>
                <ul>
                    <li>
                        You can also type in more detailed comments which will be included as an Addendum at the bottom of
                        your
                        documents.
                    </li>
                    <li>
                        You can use this to include contracts, conditions, promotions and legal writings.
                    </li>
                    <li>
                        From Settings > Management Listings > References, you can define Addendums that you want to add into
                        your estimates,
                        orders, invoices and any other commercial document reference.
                    </li>
                </ul>
            </div>

            <div style="position: fixed; left: 0; bottom: 0; width: 100%;">
            @if(@$hide_signed_box_show == 0)
                @if(@$show_signed_title)
                    <p style="font-weight: bold;">{{@$signed_box_text ?? 'Signed:'}}</p>
                @endif
            @endif
                <table style="border-collapse: collapse; vertical-align: top; width: 100%;">
                    <tr>
                    @if(@$hide_signed_box_show == 0)
                        <td style="margin: 0;">
                            <div style="border: 1px solid gray; padding: 100px 70px 10px 10px;">
                                <!-- <img width="100" height="80" object-fit="cover"
                                    src="https://camo.githubusercontent.com/fcd5a5ab2be5419d00fcb803f14c55652cf60696d7f6d9828b99c1783d9f14a3/68747470733a2f2f662e636c6f75642e6769746875622e636f6d2f6173736574732f393837332f3236383034362f39636564333435342d386566632d313165322d383136652d6139623137306135313030342e706e67" /> -->
                                <span style="font-weight: bold; position: relative; bottom: 0;">Name:</span><br>
                                <span style="font-weight: bold; position: relative; bottom: 0;">Ced/Ruc:</span>
                            </div>
                        </td>
                     @endif
                        
                        <td style="padding: 0; margin: 0; padding-left: 120px;">
                            <div>
                                <table style="border-collapse: collapse; width: 100%; ">
                                    <tr style="border-bottom: 1px solid gray;">
                                        <th class="table_heading" style="padding: 5px 0; text-align: left;">BASE</th>
                                        <th></th>
                                        <th class="table_heading" style="padding: 5px 0; text-align: right;">$ {{ number_format($subtotal, 2) }}</th>
                                    </tr>
                                    <tr style="border-bottom: 1px solid gray;">
                                        <td style="padding: 5px 0;  margin: 0; text-align: left;">{{ number_format($subtotal, 2) }}</td>
                                        <td style="padding: 5px 0; text-align: center"><span> VAT 21%</span></td>
                                        <td style="padding: 5px 0; text-align: right">{{ $vat }}</td>
                                    </tr>
                                    <tr>
                                        <th class="table_heading" style="padding: 5px 0; text-align: left">TOTAL</th>
                                        <td style="padding: 0; margin: 0;"></td>
                                        <th style="text-align: right">$ {{ number_format($total, 2) }}</th>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            
        @else
        
        <div style="position: fixed; left: 0; bottom: 0; width: 100%;">
            @if(@$hide_signed_box_show == 0)
                @if(@$show_signed_title)
                    <p style="font-weight: bold;">{{@$signed_box_text ?? 'Signed:'}}</p>
                @endif
            @endif
            <table style="border-collapse: collapse; vertical-align: top; width: 100%;">
                <tr>
                    @if(@$hide_signed_box_show == 0)
                            <td style="margin: 0;">
                            <div style="border: 1px solid gray; padding: 100px 70px 10px 10px;">
                                <!-- <img width="100" height="80" object-fit="cover"
                                    src="https://camo.githubusercontent.com/fcd5a5ab2be5419d00fcb803f14c55652cf60696d7f6d9828b99c1783d9f14a3/68747470733a2f2f662e636c6f75642e6769746875622e636f6d2f6173736574732f393837332f3236383034362f39636564333435342d386566632d313165322d383136652d6139623137306135313030342e706e67" /> -->
                                @if(@$sign_signature_title)
                                    <span style="font-weight: bold; position: relative; bottom: 0;">{{@$sign_signature_text ?? 'Name:'}}</span><br>
                                @endif
                                @if(@$show_signed_tin_title)
                                    <span style="font-weight: bold; position: relative; bottom: 0;">{{@$signed_tin_text ?? 'Ced/Ruc:'}}</span>
                                @endif
                            </div>
                        </td>
                    @endif
                    <td style="padding: 0; margin: 0; padding-left: 120px;">
                        <div>
                            <table style="border-collapse: collapse; width: 100%; ">
                                <tr style="border-bottom: 1px solid gray;">
                                    <th class="table_heading" style="padding: 5px 0; text-align: left;">BASE</th>
                                    <th></th>
                                    <th class="table_heading" style="padding: 5px 0; text-align: right;">$ {{ number_format($subtotal, 2) }}</th>
                                </tr>
                                <tr style="border-bottom: 1px solid gray;">
                                    <td style="padding: 5px 0;  margin: 0; text-align: left;">{{ number_format($subtotal, 2) }}</td>
                                    <td style="padding: 5px 0; text-align: center"><span> VAT 21%</span></td>
                                    <td style="padding: 5px 0; text-align: right">{{ $vat }}</td>
                                </tr>
                                <tr>
                                    <th class="table_heading" style="padding: 5px 0; text-align: left">TOTAL</th>
                                    <td style="padding: 0; margin: 0;"></td>
                                    <th style="text-align: right">$ {{ number_format($total, 2) }}</th>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>
            @if(@$comments_title_show)
                <div style="margin-top: 15px;">
                    <table style="border-collapse: collapse; width: 100%; ">
                        <tr>
                            <th class="table_heading" style="padding: 5px 0; text-align: left; border-bottom: 1px solid gray;">{{@$comments_title_text}}</th>
                        </tr>
                    </table>
                </div>
            @endif
            @if(@$show_footer_count)
                <!-- <div style="margin-left:15%;margin-top:20px;width: 70%; border-bottom: 1px solid black; text-align: right;">1/1</div> -->
                <div style="margin-top:20px; margin-left:20px;">
                    @if(@$legal_note_show)
                        <span style="text-align: left;">{{ @$legal_note_text }} </span><br>
                    @endif
                        <span class="mdaa" style="border-collapse: collapse; width: 100%; ">1/1</span>
                </div>
            @endif
        </div>
        @endif
    </div>
</body>