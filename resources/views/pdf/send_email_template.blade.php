@php
    $company_email_show = [];
    $company_logo_show = 0;
    $company_website_show = [];
    $company_name_show = 0;
    $company_country_show = 0;
    $company_city_show = 0;
    $company_commercial_name_show = 0;
    $company_pincode_show = 0;
    $company_address_show = 0;
    $client_supplier_tin_show = 0;
    $document_reference_show = 0;
    $company_tin_show = 0;
    $company_phone_show = 0;
    $document_payment_info_show = 0;
    $document_status_show = 0;
    $document_status_text = 'Status:';
    $document_created_by_show = 0;
    $document_created_by_text = 'Created by:';
    $company_company_info_show = 0;
    $document_title_show = 0;
    $document_title_text = '';
    $font = @$template->font ?? 'DejaVu Sans';
    $color = @$template->color;
@endphp
  
    <style>
        
        .table_heading{
            color: {{ $color }} !important;
        }
        .header_border {
            border-left: 2px solid {{ $color }} !important;
        }
        .section{
            margin-left: 100px;
        }
        .sectionLeft{
            /* margin-left: 50px; */
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
    
    

    @foreach(@$template->metas as $meta)
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
        @if($meta->category == 'Company Information' && $meta->type == 'commercial_name' && $meta->option_name == 'show')
            @php
            $company_commercial_name_show = $meta->option_value;
            @endphp
        @endif
        @if($meta->category == 'Company Information' && $meta->type == 'pincode' && $meta->option_name == 'show')
            @php
            $company_pincode_show = $meta->option_value;
            @endphp
        @endif
        @if($meta->category == 'Company Information' && $meta->type == 'address' && $meta->option_name == 'show')
            @php
            $company_address_show = $meta->option_value;
            @endphp
        @endif
        @if($meta->category == 'Company Information' && $meta->type == 'tin' && $meta->option_name == 'show')
            @php
            $company_tin_show = $meta->option_value;
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

        @if($meta->category == 'Company Information' && $meta->type == 'country' && $meta->option_name == 'show')
            @php
            $company_country_show = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Company Information' && $meta->type == 'city' && $meta->option_name == 'show')
            @php
            $company_city_show = $meta->option_value;
            @endphp
        @endif
        @if($meta->category == 'Company Information' && $meta->type == 'phone' && $meta->option_name == 'show')
            @php
            $company_phone_show = $meta->option_value;
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
            $hide_signed_box_show = $meta->option_value;
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

    <div style="position:relative; font-size: 12px; font-family:{{$font}};"">
        <img src="{{ $watermark_image }}" alt="" style="position: absolute; z-index: -1; opacity: 0.3; top:50%; left: 50%; transform: translate(-50%); width: 600px">
        <div style="margin-top: 0px;height: 45px;">
        @if($company_company_info_show != 1)
            <div style="margin-top: 0px;">
                <table style="border-collapse: collapse; width:100%;">
                    <tr>
                        <td style="padding: 0; margin: 0;">
                        @if($company_logo_show)
                            <img src="{{ @$company->logo }}" alt="" srcset="" style="width: 80px; height: 80px; object-fit: cover;">
                        @endif
                        </td>
                        <span class="section">
                        <td class="header_border" style="width:250px"  @if($company_name_show || $company_country_show) @endif>
                            <span style="margin-left: 20px;">Company Name:</span>
                            <span>{{  @$company->commercial_name }}</span> <br>
                            @if(@$company->address)
                            <div style="margin-left: 20px;">
                                
                                <span >Address:</span>
                                <span>{{@$company->address}}</span><br>
                            </div>
                            @endif
                            <div style="margin-left: 20px;">
                            <span>{{@$company->pincode}} {{@$company->city}} {{@$company->country}} {{@$company->tin}}</span><br>
                            </div>
        
                        </td>
                        </span>
                        <span class="sectionLeft">
                            <td class="header_border" @if(@$company_email_show['show'] || @$company_website_show['show']) style="width: 300px; " @endif>
                                @if(@$company_email_show['show'] ==1)
                                    <span style="margin-left: 20px;">Email:</span> 
                                    @if(@$company_email_show['show'] ==1 && @$company_email_show['value'])
                                        {{$company_email_show['value']}}
                                    @elseif(@$company_email_show['show'] ==1 && @!$company_email_show['value'])
                                        {{ $company->email}}
                                    @endif
                                    <br>
                                @endif
                                @if($company->website)
                                    @if(@$company_website_show['show'] == 1)    
                                        <span style="margin-left: 20px;">
                                        {{ @$company_website_show['value'] ? @$company_website_show['value'] : 'Website:'}} </span> @if(@$company_website_show['show'] ==1 && @$company_website_show['value'])
                                            <!-- {{$company_website_show['value']}} -->{{ $company->website}}
                                        @elseif(@$company_website_show['show'] ==1 && @!$company_website_show['value'])
                                            {{ $company->website}}
                                        @endif
                                        <br>
                                    @endif
                                @endif
                                @if(@$company->phone)
                                    <span style="margin-left: 20px;">Phone: {{ $company_phone_show == 1 ? $company->phone : '' }}</span>
                                @endif
                            </td>
                        </span>
                    </tr>
                </table>
            </div>
        @endif
        </div>
        <div style="text-align: center; margin-top: 70px;">
            @if(@$document_type_show == 1)
                    <h2>{{ ($request->format == 'pro_forma') ? 'PRO FORMA' : $template->name }}</h2>
                    @if($document_title_show && $document_title_text)
                        {{ $document_title_text }}
                    @endif
            @endif
        </div>
        <div style="height:400px">
            <div style="margin-top: 0px;font-size: 13px">
                <table style="border-collapse: collapse; width:50%; padding: 10px; float: left;">
                    <th class="table_heading" style=" border-bottom: 1px solid gray;text-align: left;">{{ strtoupper(($request->format == 'pro_forma') ? 'PRO FORMA' : $template->document_type) }} INFO</th>

                    <tr><td style="padding: 0; margin: 0;">Number: <b>{{ @$invoiceData->reference.''.@$invoiceData->reference_number }}</b></td></tr>
                    <!-- @if(@$invoiceData->client->legal_name)
                        <tr><td style="padding: 0; margin: 0;">Client Name: <b>{{ @$invoiceData->client->legal_name }}</b></td></tr>
                    @endif -->
                    {{-- @if(@$invoiceData->client->tin)
                        <tr><td style="padding: 0; margin: 0;">Ced/Ruc: <b>{{ @$invoiceData->client->tin }}</b></td></tr>
                    @endif --}}
                    @if(@$invoiceData->date)
                     <tr><td style="padding: 0; margin: 0;">Date: <b>{{ @$invoiceData->date }}</b></td></tr>
                    @endif
                    @if(@$invoiceData->status)
                     <tr><td style="padding: 0; margin: 0;">Status: <b>{{ @$invoiceData->status }}</b></td></tr>
                    @endif

                    @if($document_payment_info_show == 1 && @$invoiceData->payment_options->name)
                        <tr>
                            <td style="padding: 0; margin: 0;">
                                {{ $document_payment_info_text ? $document_payment_info_text : 'Payment Option:'}} <b>{{ @$invoiceData->payment_options->name }}</b>
                            </td>
                        </tr>
                    @endif
                    
                    @if($document_created_by_show == 1 && @$invoiceData->created_by)
                        <tr>
                            <td style="padding: 0; margin: 0;">
                                {{ $document_created_by_text ? $document_created_by_text : 'Created by:'}} <b>{{ @$invoiceData->created_by }}</b>
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td style="padding: 0; margin: 0;">
                        @if($invoiceData->delivery_address)
                            Delivery to: <b>{{ @$invoiceData->delivery_address }}</b>
                        @endif
                        </td>
                    </tr>

                    @if(@$document_delivery_by_show == 1 && @$invoiceData->delivery_options->name)
                        <tr>
                            <td style="padding: 0; margin: 0;">
                                {{ $document_delivery_by_text ? $document_delivery_by_text : 'Delivery Option:'}}
                                        <b>{{ @$invoiceData->delivery_options->name }}</b>
                            </td>
                        </tr>
                    @endif

                    @if(@$document_agent_show == 1)
                        <tr>
                            <td style="padding: 0; margin: 0;">
                                {{ $document_agent_text ? $document_agent_text : 'Agent:'}} <b>Test View Account</b>
                            </td>
                        </tr>
                    @endif
                </table>
                <table style="border-collapse: collapse; width:50%; padding: 10px; float: right;">
                    @if($invoiceData->client)
                        <th class="table_heading" style=" border-bottom: 1px solid gray;text-align: left; height:16px">CLIENT OF DATA
                        @if(@$client_supplier_section_show == 1)
                            {{($client_supplier_section) ? $client_supplier_section : ""}}
                        @endif

                        </th>
                    @elseif($invoiceData->supplier)
                    <th class="table_heading" style=" border-bottom: 1px solid gray;text-align: left; height:16px">SUPPLIER OF DATA
                        @if(@$client_supplier_section_show == 1)
                            {{($client_supplier_section) ? $client_supplier_section : ""}}
                        @endif

                        </th>
                    @endif
        
                    @if(@$client_supplier_name_show || @$client_supplier_legal_name_show)
                        @if($invoiceData->client->legal_name)
                            <tr><td style="padding: 0; margin: 0;">Name/Legal Name: <b>{{@$client_supplier_legal_name}} {{@$invoiceData->client->legal_name}} {{@$client_supplier_name.' '.@$invoiceData->client->name}}</b></td></tr>
                        @endif
                    @endif
                    @if(@$client_supplier_tin_show == 1)
                        @if($invoiceData->client->tin)
                            <tr>
                                <td style="padding: 0; margin: 0;">
                                {{$client_supplier_tin_text ? $client_supplier_tin_text : 'Ced/Ruc:'}}  <b>{{@$invoiceData->client->tin}}</b>
                                </td>
                            </tr>
                        @endif
                    @endif

                    @if(@$client_supplier_phone_show)
                        @if($invoiceData->client->phone_1)
                            <tr><td style="padding: 0; margin: 0;">Phone: <b>{{$client_supplier_phone}} {{@$invoiceData->client->phone_1}}</b></td></tr>
                        @endif        
                    @endif

                    @if(@$client_supplier_reference_show)
                        @if($invoiceData->client->reference)
                            <tr><td style="padding: 0; margin: 0;">Reference: <b>{{$client_supplier_reference}} {{@$invoiceData->client->reference.''.@$invoiceData->client->reference_number}}</b></td></tr>
                        @endif
                    @endif

                    @if(@$client_supplier_fax_show)
                        @if($invoiceData->client->fax)
                            <tr><td style="padding: 0; margin: 0;">Fax: <b>{{$client_supplier_fax}} {{@$invoiceData->client->fax}}</b></td></tr>
                        @endif
                    @endif

                    @if(@$client_supplier_email_show)
                        @if($invoiceData->client->email)
                            <tr><td style="padding: 0; margin: 0;">Email: <b>{{$client_supplier_email}} {{@$invoiceData->client->email}}</b></td></tr>
                        @endif
                    @endif

                    @if(@$client_supplier_website_show)
                        @if($invoiceData->client->website)
                            <tr><td style="padding: 0; margin: 0;">Website: <b>{{$client_supplier_website}} {{@$invoiceData->client->website}}</b></td></tr>
                        @endif
                    @endif

                    @if(@$client_supplier_billing_show)
                        <tr><td style="padding: 0; margin: 0;">Billing: <b>{{$client_supplier_billing}}</b></td></tr>
                    @endif

                    @if($invoiceData->client)
                    <tr><td style="padding: 0; margin: 0;">
                    @if(@$client_supplier_zip_code_show == 1) 
                        @if($invoiceData->client->zip_code)
                            Zip Code: <b>{{ $invoiceData->client->zip_code }}</b>
                        @endif
                    @endif
                        </td>
                    </tr>
                
                    <tr>
                    {{$client_supplier_city_show}}
                    <td style="padding: 0; margin: 0;">
                    @if(@$client_supplier_city_show == 1)
                        {{-- <b>{{$client_supplier_city_show}}</b>
                    @else --}}
                        @if($invoiceData->client->city)
                            City: <b>{{ $invoiceData->client->city }}</b>
                        @endif
                    @endif
                        </td>
                    </tr>

                    <tr><td style="padding: 0; margin: 0;">
                    @if(@$client_supplier_state_show == 1) 
                        {{-- <b>{{$client_supplier_state_show}}</b>
                    @else --}}
                        @if($invoiceData->client->state)
                            State: <b>{{ $invoiceData->client->state }}</b>
                        @endif
                    @endif
                        </td>
                    </tr>

                    <tr><td style="padding: 0; margin: 0;">
                    @if(@$client_supplier_country_show == 1)
                        {{-- <b>{{$client_supplier_country_show}}</b>
                    @else --}}
                        @if($invoiceData->client->country)
                            Country: <b>{{ $invoiceData->client->country }}</b>
                        @endif
                    @endif
                        </td>
                    </tr>
                    @elseif($invoiceData->supplier)
                    <tr><td style="padding: 0; margin: 0;">
                    @if(@$client_supplier_zip_code_show == 1) 
                        @if($invoiceData->supplier->zip_code)
                            Zip Code: <b>{{ $invoiceData->supplier->zip_code }}</b>
                        @endif
                    @endif
                        </td>
                    </tr>
                
                    <tr>
                    {{$client_supplier_city_show}}
                    <td style="padding: 0; margin: 0;">
                    @if(@$client_supplier_city_show == 1)
                        {{-- <b>{{$client_supplier_city_show}}</b>
                    @else --}}
                        @if($invoiceData->supplier->city)
                            City: <b>{{ $invoiceData->supplier->city }}</b>
                        @endif
                    @endif
                        </td>
                    </tr>

                    <tr><td style="padding: 0; margin: 0;">
                    @if(@$client_supplier_state_show == 1) 
                        {{-- <b>{{$client_supplier_state_show}}</b>
                    @else --}}
                        @if($invoiceData->supplier->state)
                            State: <b>{{ $invoiceData->supplier->state }}</b>
                        @endif
                    @endif
                        </td>
                    </tr>

                    <tr><td style="padding: 0; margin: 0;">
                    @if(@$client_supplier_country_show == 1)
                        {{-- <b>{{$client_supplier_country_show}}</b>
                    @else --}}
                        @if($invoiceData->supplier->country)
                            Country: <b>{{ $invoiceData->supplier->country }}</b>
                        @endif
                    @endif
                        </td>
                    </tr>
                    @endif
                    
                </table>
            </div>
            
            <div style="clear: both;"></div>
            @if($invoiceData->clientAsset)
            <div style="margin-top: 0px;font-size: 13px; height:40px">
                <table style="border-collapse: collapse; width:100%; padding: 10px; float: left;">
                    <th class="table_heading" style=" border-bottom: 1px solid gray;text-align: left;">{{ strtoupper($template->document_type) }} ASSET</th>

                    <tr>
                        @if(@$invoiceData->clientAsset->name)
                            <span><b>Name: </b></span>
                            <span>{{ $invoiceData->clientAsset->name }}</span>
                        @endif
                        @if(@$invoiceData->clientAsset->brand)
                            <span><b> Brand: </b></span>
                            <span>{{ $invoiceData->clientAsset->brand }}</span>
                        @endif
                        @if(@$invoiceData->clientAsset->model)
                            <span><b> Model: </b></span>
                            <span>{{ $invoiceData->clientAsset->model }}</span><br>
                        @endif
                        @if(@$invoiceData->clientAsset->serial_number)
                            <span><b> Serial Number: </b></span>
                            <span>{{ $invoiceData->clientAsset->serial_number }}</span>
                        @endif
                        @if(@$invoiceData->clientAsset->identifier)
                            <span><b> Identifier: </b></span>
                            <span>{{ $invoiceData->clientAsset->identifier }}</span>
                        @endif
                    </tr>
                    
                </table>
            </div>
            @endif
            <div style="margin-top: 40px;">       
                <table style="border-collapse: collapse; width:100%; ">
                    <tr class="table_heading" style=" border-bottom: 1px solid gray;">
                        @if($document_reference_show == 1)
                            <th class="table_heading" style="padding: 0 0 5px; border-bottom: 1px solid #999; text-align: left;">
                            {{ $document_reference_text ? $document_reference_text : 'REF.'}}
                            </th>
                        @endif
                        <th class="table_heading" style="padding: 0 0 5px; border-bottom: 1px solid #999; text-align: left;">DESCRIPTION</th>
                        <th class="table_heading" style="padding: 0 0 5px; border-bottom: 1px solid #999; text-align: left;">QTY.</th>
                        @if($request->format != 'without_values') 
                            <th class="table_heading" style="padding: 0 0 5px; border-bottom: 1px solid #999; text-align: left;">DISC.</th>
                            <th class="table_heading" style="padding: 0 0 5px; border-bottom: 1px solid #999; text-align: left;">PRICE</th>
                        @endif
                        @if($request->format != 'without_values')
                            @if($request->format != 'without_totals')
                                <th class="table_heading" style="padding: 0 0 5px; border-bottom: 1px solid #999; text-align: left;">SUBTOTAL</th>
                            @endif
                        @endif
                    </tr>
                    @php
                    $subtotal = 0;
                    @endphp
                    @if(count(@$products))
                        @foreach($products as $product)
                            <tr>
                                <td style="padding: 0 0 5px; margin: 0; border-bottom: 1px solid #999;">
                                    <p style="marging: 0; padding: 0">{{ $product->reference }}</p>
                                    
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
                                <td style="padding: 0 0 5px; margin: 0; border-bottom: 1px solid #999;">
                                    <p style="marging: 0; padding: 0">{{ $product->name }}</p>
                                    <span>{{ $product->description }}</span>
                                </td>
                                <td style="padding: 0 0 5px; margin: 0; border-bottom: 1px solid #999;">
                                    <p style="marging: 0; padding: 0">{{ $product->quantity }}</p>
                                </td>
                                @if($request->format != 'without_values') 
                                    <td style="padding: 0 0 5px; margin: 0; border-bottom: 1px solid #999;">
                                        <p style="marging: 0; padding: 0">{{ @$product->discount }}</p>
                                    </td>
                                    <td style="padding: 0 0 5px; margin: 0; border-bottom: 1px solid #999;">
                                        <p style="marging: 0; padding: 0">{{ @$product->base_price }}</p>
                                    </td>
                                @endif
                                @if($request->format != 'without_values')
                                    @if($request->format != 'without_totals')
                                        <td style="padding: 0 0 5px; margin: 0; border-bottom: 1px solid #999;">
                                            <p style="marging: 0; padding: 0">{{ @$product->amount_with_out_vat }}</p>
                                        </td>
                                    @endif
                                @endif
                                @php
                                $subtotal += (float)$product->base_price;
                                @endphp
                            </tr>
                        @endforeach
                    @endif
                </table>
            </div>
        </div>
            @php
            $vat = $total*(float)@$product->vat/100;
            $totals = $total+$vat;
            @endphp

            
        
        @if(@$comments_show == 1)
        @if($request->format != 'without_values') 
            @if($request->format != 'before_tax') 
                @if($request->format != 'pro_forma')
                   @if($request->format != 'without_totals')
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
                        <div>
                            <p style="font-weight: bold;">Signed:</p>
                        </div>
                    @endif
                @endif
            @endif
            @if($request->format != 'without_totals')
                <div style="position: fixed; left: 0; bottom: 0; width: 100%;">
                    <table style="border-collapse: collapse; vertical-align: top; width: 100%;">
                        <tr>
                        @if($request->format != 'before_tax') 
                            @if($request->format != 'pro_forma') 
                                 @if(($request->disable_signed)? '0':'1')     
                                    @if(@$hide_signed_box_show == 0)
                                        <td style="margin: 0;">
                                            <div style="border: 1px solid gray; padding: 10px;">
                                                <img width="100" height="80" object-fit="cover"
                                                    src="https://camo.githubusercontent.com/fcd5a5ab2be5419d00fcb803f14c55652cf60696d7f6d9828b99c1783d9f14a3/68747470733a2f2f662e636c6f75642e6769746875622e636f6d2f6173736574732f393837332f3236383034362f39636564333435342d386566632d313165322d383136652d6139623137306135313030342e706e67" />
                                                <p style="font-weight: bold; position: relative; bottom: 0;">Name:</p>
                                                <p style="font-weight: bold; position: relative; bottom: 0;">Ced/Ruc:</p>
                                            </div>
                                        </td>
                                        @endif
                                @endif
                            @endif
                        @endif
                            <td style="padding: 0; margin: 0; padding-left: 120px;">
                                <div>
                                    <table style="border-collapse: collapse; width: 100%; ">
                                        <tr style="border-bottom: 1px solid gray;">
                                            <th class="table_heading" style="padding: 5px 0; text-align: left;">BASE</th>
                                            <th></th>
                                            <th class="table_heading" style="padding: 5px 0; text-align: right;">$ {{ number_format($total,2) }}</th>
                                        </tr>
                                        @if($request->format != 'before_tax') 
                                            <tr style="border-bottom: 1px solid gray;">
                                                <td style="padding: 5px 0;  margin: 0; text-align: left;">{{ number_format($total,2) }}</td>
                                                <td style="padding: 5px 0; text-align: center"><span>IVA {{@$product->vat}}%</span></td>
                                                <td style="padding: 5px 0; text-align: right">{{ $invoiceData->taxAmount }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th class="table_heading" style="padding: 5px 0; text-align: left">TOTAL</th>
                                            <td style="padding: 0; margin: 0;"></td>
                                            <th style="text-align: right">$ {{ $invoiceData->amount }}</th>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            @endif
        @endif
        @else
        
        <div style="position: fixed; left: 0; bottom: 0; width: 100%;">
        <p style="font-weight: bold;">Signed:</p><br
            <table style="border-collapse: collapse; vertical-align: top; width: 100%;">
                <tr>
                    @if(@$hide_signed_box_show == 0)
                        <td style="margin: 0;">
                            <div style="border: 1px solid gray; padding: 10px;">
                                <img width="100" height="80" object-fit="cover"
                                    src="https://camo.githubusercontent.com/fcd5a5ab2be5419d00fcb803f14c55652cf60696d7f6d9828b99c1783d9f14a3/68747470733a2f2f662e636c6f75642e6769746875622e636f6d2f6173736574732f393837332f3236383034362f39636564333435342d386566632d313165322d383136652d6139623137306135313030342e706e67" />
                                <p style="font-weight: bold; position: relative; bottom: 0;">Name:</p>
                                <p style="font-weight: bold; position: relative; bottom: 0;">Ced/Ruc:</p>
                            </div>
                        </td>
                        @endif
                    <td style="padding: 0; margin: 0; padding-left: 120px;">
                                <div>
                                    <table style="border-collapse: collapse; width: 100%; ">
                                        <tr style="border-bottom: 1px solid gray;">
                                            <th class="table_heading" style="padding: 5px 0; text-align: left;">BASE</th>
                                            <th></th>
                                            <th class="table_heading" style="padding: 5px 0; text-align: right;">$ {{ number_format($total,2) }}</th>
                                        </tr>
                                        @if($request->format != 'before_tax') 
                                            <tr style="border-bottom: 1px solid gray;">
                                                <td style="padding: 5px 0;  margin: 0; text-align: left;">{{ number_format($total,2) }}</td>
                                                <td style="padding: 5px 0; text-align: center"><span>IVA {{@$product->vat}}%</span></td>
                                                <td style="padding: 5px 0; text-align: right">{{ $invoiceData->taxAmount }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th class="table_heading" style="padding: 5px 0; text-align: left">TOTAL</th>
                                            <td style="padding: 0; margin: 0;"></td>
                                            <th style="text-align: right">$ {{ $invoiceData->amount }}</th>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                </tr>
            </table>
        </div>
        @endif
    </div>
</body>