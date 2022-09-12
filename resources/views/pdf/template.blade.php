<body>
    @php
    $company_email_show = [];
    $company_logo_show = 0;
    $company_website_show = [];
    $company_name_show = 0;
    $company_country_show = 0;
    $document_payment_info_show = 0;
    $document_status_show = 0;
    $document_status_text = 'Status:';
    $document_created_by_show = 0;
    $document_created_by_text = 'Created by:';
    $company_company_info_show = 0;
    $document_title_show = 0;
    $document_title_text = '';
    @endphp

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

        @if($meta->category == 'Company Information' && $meta->type == 'hide_company_information' && $meta->option_name == 'show')
            @php
            $company_company_info_show = $meta->option_value;
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

        @if($meta->category == 'Document Information' && $meta->type == 'section' && $meta->option_name == 'text')
            @php
            $document_section_title_text = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Document Information' && $meta->type == 'section' && $meta->option_name == 'show')
            @php
            $document_section_title_show = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Document Information' && $meta->type == 'client_legal_name' && $meta->option_name == 'show')
            @php
            $document_section_title_show = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Document Information' && $meta->type == 'client_legal_name' && $meta->option_name == 'text')
            @php
            $document_section_title_text = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Document Information' && $meta->type == 'client_name' && $meta->option_name == 'show')
            @php
            $client_name_show = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Document Information' && $meta->type == 'client_name' && $meta->option_name == 'text')
            @php
            $client_name_text = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Document Information' && $meta->type == 'client_tin' && $meta->option_name == 'show')
            @php
            $client_tin_show = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Document Information' && $meta->type == 'client_tin' && $meta->option_name == 'text')
            @php
            $client_tin_text = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Document Information' && $meta->type == 'client_phone' && $meta->option_name == 'show')
            @php
            $client_phone_show = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Document Information' && $meta->type == 'client_phone' && $meta->option_name == 'text')
            @php
            $client_phone_text = $meta->option_value;
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
    @endforeach

    @if(strpos($template->watermark,"via.placeholder") !== false)
        @php
        $watermark_image = 'default_watermark.png';
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

    <div style="position:relative; font-size: 13px; font-family:Helvetica;">
        <img src="{{ $watermark_image }}" alt="" style="position: absolute; z-index: -1; opacity: 0.3; top:50%; left: 50%; transform: translate(-50%); width: 600px">
        @if($company_company_info_show != 1)
            <div style="margin-top: 0px;">
                <table style="border-collapse: collapse; width:100%">
                    <tr>
                        <td style="padding: 0; margin: 0;">
                        @if($company_logo_show)
                            <img src="{{ asset('light.png') }}" alt="" srcset="" style="width: 120px; height: auto; object-fit: cover;">
                        @endif
                        </td>
                        <td @if($company_name_show || $company_country_show) style="border-left: 2px solid orange;" @endif>
                            <span style="margin-left: 30px;">{{ $company_name_show == 1 ? $company->name : '' }}</span> <br>
                            <span style="margin-left: 30px;">{{ $company_country_show == 1 ? $company->country : '' }}</span>
                        </td>
                        <td @if(@$company_email_show['show'] || @$company_website_show['show']) style="border-left: 2px solid orange; width: 300px; " @endif>
                            @if(@$company_email_show['show'] ==1)
                                <span style="margin-left: 30px;">Email:</span> 
                                @if(@$company_email_show['show'] ==1 && @$company_email_show['value'])
                                    {{$company_email_show['value']}}
                                @elseif(@$company_email_show['show'] ==1 && @!$company_email_show['value'])
                                    {{ $company->email}}
                                @endif
                                <br>
                            @endif
                            @if(@$company_website_show['show'])    
                                <span style="margin-left: 30px;">website</span> @if(@$company_website_show['show'] ==1 && @$company_website_show['value'])
                                    {{$company_website_show['value']}}
                                @elseif(@$company_website_show['show'] ==1 && @!$company_website_show['value'])
                                    {{ $company->website}}
                                @endif
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        @endif

        @if(@$document_type_show == 1)
            <div style="text-align: center; margin-top: 20px;">
                <h2>{{ $template->name }}</h2>
                @if($document_title_show && $document_title_text)
                    {{ $document_title_text }}
                @endif
            </div>
        @endif
        <div style="margin-top: 20px;font-size: 13px">
            <table style="border-collapse: collapse; width:50%; padding: 10px; float: left;">
                <th style="color: orange; border-bottom: 1px solid gray;text-align: left">{{ strtoupper($template->document_type) }} INFO</th>
                <tr><td style="padding: 0; margin: 0;">Number: <b>INV00001</b></td></tr>
                <tr><td style="padding: 0; margin: 0;">Date: <b>{{ date('d F Y') }}</b></td></tr>
                @if($document_payment_info_show == 1)
                    <tr>
                        <td style="padding: 0; margin: 0;">
                            {{ $document_payment_info_text ? $document_payment_info_text : 'Payment Option:'}}<b>Online Bank Transfer</b>
                        </td>
                    </tr>
                @endif

                @if($document_status_show == 1)
                    <tr>
                        <td style="padding: 0; margin: 0;">
                            {{ $document_status_text ? $document_status_text : 'Status:'}}<b>Pending</b>
                        </td>
                    </tr>
                @endif
                
                @if($document_created_by_show == 1)
                    <tr>
                        <td style="padding: 0; margin: 0;">
                            {{ $document_created_by_text ? $document_created_by_text : 'Created by:'}}<b>Test View Account</b>
                        </td>
                    </tr>
                @endif

                <tr>
                    <td style="padding: 0; margin: 0;">
                        Delivery to: <b>HongKong 9205 Olive Ave., 10977, Spring Valley, NY, United States</b>
                    </td>
                </tr>

                @if(@$document_delivery_by_show == 1)
                    <tr>
                        <td style="padding: 0; margin: 0;">
                            {{ $document_delivery_by_text ? $document_delivery_by_text : 'Delivery Option:'}} <b>test delivery A domicilio</b>
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
                <th style="color: orange; border-bottom: 1px solid gray;text-align: left">{{ strtoupper($template->section) }} INFO</th>
                <tr><td style="padding: 0; margin: 0;"><b>{{$template->client_legal_name}}({{$template->client_name}})</b></td></tr>
                <tr><td style="padding: 0; margin: 0;"><b>{{$template->client_tin}}</b></td></tr>
                <tr><td style="padding: 0; margin: 0;"><b>
                @if(@$client_phone_show['show'] ==1)
                    @if(@$client_phone_show['show'] ==1 && @$client_phone_show['value'])
                        {{$client_phone_show['value']}}
                    @elseif(@$client_phone_show['show'] ==1 && @!$client_phone_show['value'])
                        {{ $company->client_phone}}
                    @endif
                @endif
               </b></td></tr>
            </table>
        </div>
        <div style="clear: both;"></div>
        <div style="margin-top: 20px;">
            <table style="border-collapse: collapse; width:100%; ">
                <tr style="color: orange; border-bottom: 1px solid gray;">
                    <th style="color: orange; padding: 0 0 5px; border-bottom: 1px solid #999; text-align: left;">REF.</th>
                    <th style="color: orange; padding: 0 0 5px; border-bottom: 1px solid #999; text-align: left;">NAME</th>
                    <th style="color: orange; padding: 0 0 5px; border-bottom: 1px solid #999; text-align: left;">PRICE</th>
                    <th style="color: orange; padding: 0 0 5px; border-bottom: 1px solid #999; text-align: left;">DISC.</th>
                    <th style="color: orange; padding: 0 0 5px; border-bottom: 1px solid #999; text-align: left;">QTY.</th>
                    <th style="color: orange; padding: 0 0 5px; border-bottom: 1px solid #999; text-align: left;">SUBTOTAL</th>
                    <th style="color: orange; padding: 0 0 5px; border-bottom: 1px solid #999; text-align: left;">TAXES</th>
                </tr>
                @php
                $subtotal = 0;
                @endphp
                @foreach($products as $product)
                    <tr>
                        <td style="padding: 0 0 5px; margin: 0; border-bottom: 1px solid #999;">
                            <p style="marging: 0; padding: 0">{{ $product->id }}</p>
                            
                            @if(strpos($product->image,"via.placeholder") !== false)
                                @php
                                $image = 'https://dummyimage.com/67x69/dfdfdf/000000.png&text=Not+Found';
                                @endphp
                            @else
                                @php
                                $image = $product->image;
                                @endphp
                            @endif

                            <img height="45" src="{{ $image }}" alt="" srcset="">
                        </td>
                        <td style="padding: 0 0 5px; margin: 0; border-bottom: 1px solid #999;">
                            <p style="marging: 0; padding: 0">{{ $product->name }}</p>
                            <span>{{ $product->description }}</span>
                        </td>
                        <td style="padding: 0 0 5px; margin: 0; border-bottom: 1px solid #999;">
                            <p style="marging: 0; padding: 0">{{ $product->price }}</p>
                        </td>
                        <td style="padding: 0 0 5px; margin: 0; border-bottom: 1px solid #999;">
                            <p style="marging: 0; padding: 0">0</p>
                        </td>
                        <td style="padding: 0 0 5px; margin: 0; border-bottom: 1px solid #999;">
                            <p style="marging: 0; padding: 0">1</p>
                        </td>
                        <td style="padding: 0 0 5px; margin: 0; border-bottom: 1px solid #999;">
                            <p style="marging: 0; padding: 0">{{ $product->price }}</p>
                        </td>
                        <td style="padding: 0 0 5px; margin: 0; border-bottom: 1px solid #999;">
                            <p style="marging: 0; padding: 0">VAT 21%</p>
                        </td>
                        @php
                        $subtotal += $product->price;
                        @endphp
                    </tr>
                @endforeach
            </table>
        </div>

        @php
        $vat = $subtotal*21/100;
        $total = $vat+$subtotal;
        @endphp

        <div>
            <p style="font-weight: bold;">Signed:</p>
        </div>
        <div>
            <table style="border-collapse: collapse; vertical-align: top; width: 100%;">
                <tr>
                    <td style="margin: 0;">
                        <div style="border: 1px solid gray; padding: 10px;">
                            <img width="210" height="100" object-fit="cover"
                                src="https://camo.githubusercontent.com/fcd5a5ab2be5419d00fcb803f14c55652cf60696d7f6d9828b99c1783d9f14a3/68747470733a2f2f662e636c6f75642e6769746875622e636f6d2f6173736574732f393837332f3236383034362f39636564333435342d386566632d313165322d383136652d6139623137306135313030342e706e67" />
                            <p style="font-weight: bold; position: relative; bottom: 0;">Name:</p>
                            <p style="font-weight: bold; position: relative; bottom: 0;">TIN:</p>
                        </div>
                    </td>
                    <td style="padding: 0; margin: 0; padding-left: 120px;">
                        <div>
                            <table style="border-collapse: collapse; width: 100%; ">
                                <tr style="border-bottom: 1px solid gray;">
                                    <th style="color: orange; padding: 5px 0; text-align: left;">BASE</th>
                                    <th></th>
                                    <th style="color: orange; padding: 5px 0; text-align: right;">$ {{ $subtotal }}</th>
                                </tr>
                                <tr style="border-bottom: 1px solid gray;">
                                    <td style="padding: 5px 0;  margin: 0; text-align: left;">{{ $subtotal }}</td>
                                    <td style="padding: 5px 0; text-align: center"><span> VAT 21%</span></td>
                                    <td style="padding: 5px 0; text-align: right">{{ $vat }}</td>
                                </tr>
                                <tr>
                                    <th style="color: orange; padding: 5px 0; text-align: left">TOTAL</th>
                                    <td style="padding: 0; margin: 0;"></td>
                                    <th style="text-align: right">$ {{ $total }}</th>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <br>
        <br>
        <br>
        <br>
        <br>
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
        @endif
    </div>
</body>