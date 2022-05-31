<body>
    @php
    $company_email_show = 0;
    $company_website_show = 0;
    $company_name_show = 0;
    $company_country_show = 0;
    $document_payment_info_show = 0;
    $document_status_show = 0;
    $document_status_text = 'Status:';
    $document_created_by_show = 0;
    $document_created_by_text = 'Created by:';
    @endphp

    @foreach($template->metas as $meta)
        @if($meta->category == 'Company Information' && $meta->type == 'name' && $meta->option_name == 'show')
            @php
            $company_name_show = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Company Information' && $meta->type == 'email' && $meta->option_name == 'show')
            @php
            $company_email_show = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Company Information' && $meta->type == 'website' && $meta->option_name == 'show')
            @php
            $company_website_show = $meta->option_value;
            @endphp
        @endif

        @if($meta->category == 'Company Information' && $meta->type == 'country' && $meta->option_name == 'show')
            @php
            $company_country_show = $meta->option_value;
            @endphp
        @endif

        {{-- {{ $meta }} --}}

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

    <div style="position:relative;">
        <img src="default_watermark.png" alt="" style="position: absolute; z-index: -1; opacity: 0.3; top:50%; left: 50%; transform: translate(-50%); width: 600px">
        <div style="margin-top: 20px;">
            <table style="width:100%">
                <tr>
                    <td>
                        <img src="{{ asset('light.png') }}" alt="" srcset="" style="width: 120px; height: auto; object-fit: cover;">
                    </td>
                    <td style="border-left: 2px solid orange;">
                        <span style="margin-left: 60px;">{{ $company_name_show == 1 ? $company->name : '' }}</span> <br>
                        <span style="margin-left: 60px;">{{ $company_country_show == 1 ? $company->country : '' }}</span>
                    </td>
                    <td style="border-left: 2px solid orange;">
                        <span style="margin-left: 60px;">Email</span> {{ $company_email_show == 1 ?  $company->email : '' }}<br>
                        <span style="margin-left: 60px;">website</span> {{ $company_website_show == 1 ?  $company->website : '' }}
                    </td>
                </tr>
            </table>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <h2>{{ $template->name }}</h2>
        </div>
        <div style="margin-top: 20px;font-size: 13px">
            <table style="width:50%; padding: 10px">
                <th style="color: orange; border-bottom: 1px solid gray;text-align: left">{{ strtoupper($template->document_type) }} INFO</th>
                <tr><td>Number: <b>INV00001</b></td></tr>
                <tr><td>Date: <b>{{ date('d F Y') }}</b></td></tr>
                @if($document_payment_info_show == 1)
                    <tr>
                        <td>
                            {{ $document_payment_info_text ? $document_payment_info_text : 'Payment Option:'}}<b>Online Bank Transfer</b>
                        </td>
                    </tr>
                @endif

                @if($document_status_show == 1)
                    <tr>
                        <td>
                            {{ $document_status_text ? $document_status_text : 'Status:'}}<b>Pending</b>
                        </td>
                    </tr>
                @endif
                
                @if($document_created_by_show == 1)
                    <tr>
                        <td>
                            {{ $document_created_by_text ? $document_created_by_text : 'Created by:'}}<b>Test View Account</b>
                        </td>
                    </tr>
                @endif

                <tr>
                    <td>
                        Delivery to: <b>HongKong 9205 Olive Ave., 10977, Spring Valley, NY, United States</b>
                    </td>
                </tr>

                @if($document_delivery_by_show == 1)
                    <tr>
                        <td>
                            {{ $document_delivery_by_text ? $document_delivery_by_text : 'Delivery Option:'}} <b>test delivery A domicilio</b>
                        </td>
                    </tr>
                @endif

                @if($document_agent_show == 1)
                    <tr>
                        <td>
                            {{ $document_agent_text ? $document_agent_text : 'Agent:'}} <b>Test View Account</b>
                        </td>
                    </tr>
                @endif
            </table>
        </div>

        <div style="margin-top: 20px;">
            <table style="width:100%">
                <tr style="color: orange; border-bottom: 1px solid gray;">
                    <th style="color: orange;">REF.</th>
                    <th style="color: orange;">NAME</th>
                    <th style="color: orange;">PRICE</th>
                    <th style="color: orange;">DISC.</th>
                    <th style="color: orange;">QTY.</th>
                    <th style="color: orange;">SUBTOTAL</th>
                    <th style="color: orange;">TAXES</th>
                </tr>
                @php
                $subtotal = 0;
                @endphp
                @foreach($products as $product)
                    <tr style="border-bottom: 1px solid gray;">
                        <td>
                            <p>{{ $product->id }}</p>
                            
                            @if(strpos($product->image,"via.placeholder") !== false)
                                @php
                                $image = 'https://dummyimage.com/67x69/dfdfdf/000000.png&text=Not+Found';
                                @endphp
                            @else
                                @php
                                $image = $product->image;
                                @endphp
                            @endif

                            <img src="{{ $image }}" alt="" srcset="">
                        </td>
                        <td>
                            <p>{{ $product->name }}</p>
                            <span>{{ $product->description }}</span>
                        </td>
                        <td>
                            <p>{{ $product->price }}</p>
                        </td>
                        <td>
                            <p>0</p>
                        </td>
                        <td>
                            <p>1</p>
                        </td>
                        <td>
                            <p>{{ $product->price }}</p>
                        </td>
                        <td>
                            <p>VAT 21%</p>
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
            <table style="width: 100%;">
                <tr>
                    <td>
                        <div style="border: 1px solid gray">
                            <img width="210" height="100" object-fit="cover"
                                src="https://camo.githubusercontent.com/fcd5a5ab2be5419d00fcb803f14c55652cf60696d7f6d9828b99c1783d9f14a3/68747470733a2f2f662e636c6f75642e6769746875622e636f6d2f6173736574732f393837332f3236383034362f39636564333435342d386566632d313165322d383136652d6139623137306135313030342e706e67" />
                            <p style="font-weight: bold; position: relative; bottom: 0;">Name:</p>
                            <p style="font-weight: bold; position: relative; bottom: 0;">TIN:</p>
                        </div>
                    </td>
                    <td>
                        <div style="padding-left:150px">
                            <table style="border-bottom: 1px solid gray;">
                                <tr style="border-bottom: 1px solid gray;">
                                    <th style="color: orange;">BASE</th>
                                    <th></th>
                                    <th style="color: orange; padding-left: 30px;">$ {{ $subtotal }}</th>
                                </tr>
                                <tr style="border-bottom: 1px solid gray;">
                                    <td>{{ $subtotal }}</td>
                                    <td style="padding-left: 30px; text-align: right">VAT 21%</td>
                                    <td style="padding-left: 30px; text-align: right">{{ $vat }}</td>
                                </tr>
                                <tr>
                                    <th style="color: orange;">TOTAL</th>
                                    <td></td>
                                    <th style="padding-left: 30px">$ {{ $total }}</th>
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
        @if($comments_show == 1)
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