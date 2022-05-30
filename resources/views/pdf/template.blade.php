<body>
    <div style="position:relative;">
        <img src="watermark.png" alt="" style="position: absolute; z-index: -1; opacity: 0.3; top:50%; left: 50%; transform: translate(-50%); width: 600px">
        <div style="margin-top: 20px;">
            <table style="width:100%">
                <tr>
                    <td>
                        <img src="logoinimg.png" alt="" srcset="" style="width: 340px; height: 120px; object-fit: cover;">
                    </td>
                    <td style="border-left: 2px solid orange;">
                        <span style="margin-left: 15px;">{{ $company->name }}</span> <br>
                        <span style="margin-left: 15px;">{{ $company->country }}</span>
                    </td>
                    <td style="border-left: 2px solid orange;">
                        <span style="margin-left: 15px;">Email</span>{{ $company->email }}<br>
                        <span style="margin-left: 15px;">website</span>{{ $company->website }}
                    </td>
                </tr>
            </table>
        </div>
        <div style="text-align: center; margin-top: 20px;">
            <h2>DOCUMENT TYPE</h2>
            <span>You can insert a title here</span>
        </div>
        <div style="margin-top: 20px;font-size: 13px">
            <table style="width:50%; float: right; padding: 10px">
                <th style="color: orange; border-bottom: 1px solid gray;text-align: left">CLIENT INFO</th>
                <tr><td>Number: <b>INV00006</b></td></tr>
                <tr><td>Date: <b>24 May 2022 01/12/2015</b></td></tr>
                <tr><td>Payment Option: <b>Online Bank Transfer</b></td></tr>
                <tr><td>Status: <b>Test Pending</b></td></tr>
                <tr><td>Created by: <b>Test View Account</b></td></tr>
                <tr><td>Delivery to: <b>HongKong 9205 Olive Ave., 10977, Spring Valley, NY, United States</b></td></tr>
                <tr><td>Delivery Option: <b>test delivery A domicilio</b></td></tr>
                <tr><td>Agent: <b>Test View Account</b></td></tr>
            </table>
            <table style="width:50%;border-right: 2px solid orange; padding: 10px">
                <th style="color: orange; border-bottom: 1px solid gray;text-align: left">PURCHASE DELIVERY NOTE INFO</th>
                <tr><td>Number: 34234 <b>INV00006</b></td></tr>
                <tr><td>Date: <b>24 May 2022 01/12/2015</b></td></tr>
                <tr><td>Payment Option: <b>Online Bank Transfer</b></td></tr>
                <tr><td>Status: <b>Test Pending</b></td></tr>
                <tr><td>Created by: <b>Test View Account</b></td></tr>
                <tr><td>Delivery to: <b>HongKong 9205 Olive Ave., 10977, Spring Valley, NY, United States</b></td></tr>
                <tr><td>Delivery Option: <b>test delivery A domicilio</b></td></tr>
                <tr><td>Agent: <b>Test View Account</b></td></tr>
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
                @foreach($products as $product)
                    <tr style="border-bottom: 1px solid gray;">
                        <td>
                            <p>{{ $product->id }}</p>
                            <img src="refimg.png" alt="" srcset="">
                        </td>
                        <td>
                            <p>{{ $product->name }}</p>
                            <span>{{ $product->description }}</span>
                        </td>
                        <td>
                            <p>{{ $product->price }}</p>
                        </td>
                        <td>
                            <p>{{ $product->discount }}</p>
                        </td>
                        <td>
                            <p>{{ $product->qty }}</p>
                        </td>
                        <td>
                            <p>{{ $product->subtotal }}</p>
                        </td>
                        <td>
                            <p>VAT {{ $product->taxes }}</p>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>

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
                                    <th style="color: orange; padding-left: 30px;">$ 1,386.80</th>
                                </tr>
                                <tr style="border-bottom: 1px solid gray;">
                                    <td>1,386.80</td>
                                    <td style="padding-left: 30px; text-align: right">VAT 21.00%</td>
                                    <td style="padding-left: 30px; text-align: right">291.23</td>
                                </tr>
                                <tr>
                                    <th style="color: orange;">TOTAL</th>
                                    <td></td>
                                    <th style="padding-left: 30px">$ 1,678.03</th>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div style="margin-top: 20px;">
            <h5 style="border-bottom: 1px solid black ;">ADDENDUM</h5>
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
                    2 / 2
                </li>
            </ul>
        </div>
    </div>
</body>