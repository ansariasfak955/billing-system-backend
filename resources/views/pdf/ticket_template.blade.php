
<style>
    /* @page { size: 10cm 20cm landscape; } */
</style>
<div style="position:relative; ">
        <div style="margin-top: 0px;">
            <div style="margin-top: 0px;">
                <img src="{{ $company->logo }}" alt="" srcset="" style="width: 170px; height: 150px; margin-left:260px;">  
            </div>
                <div style="margin-top: 30px;font-size: 20px; height:50px">
                    <table style="width:100%; padding: 0px; float: left;">
                        <th style="text-align: left; margin-top: 15px;"> <b>Address</b> </th>
                        <tr>
                            <span>Ruc:</span>
                            <span>{{ @$company->tin }}</span><br>
                            <span>{{ $company->email}}</span><br>
                            <span>{{@$company->address}}</span><br>
                            <span>{{@$company->pincode}} {{@$company->city}} {{@$company->country}}</span><br>
                        </tr>
                    </table>
                    <div>
                        <center style="margin-top: 20px; font-size: 22px; font-weight: bold">
                        {{ strtoupper(($request->format == 'pro_forma') ? 'PRO FORMA' : $template->document_type) }}
                        </center>
                        <span>Number: <b>{{ @$invoiceData->reference.''.@$invoiceData->reference_number}}</b></span><br>
                        @if(@$invoiceData->payment_options->name)
                            <span>Payment Option: <b>{{$invoiceData->payment_options->name}}</b></span><br>
                        @endif
                        @if(@$invoiceData->assign->email)
                            <span>Assign To: <b>{{@$invoiceData->assign->email}}</b></span><br>
                        @endif
                        <span>Assign Date: <b>{{@$invoiceData->date}}</b></span>
                    </div>
                </div>
                <div style="margin-top: 250px;">       
                <table style="border-collapse: collapse; width:100%; ">
                    <tr style=" border-bottom: 1px solid #000000;">
                        <th class="table_heading" style="padding: 0 0 5px; border-bottom: 1px solid #000000; text-align: left;">QTY.</th>
                        <th class="table_heading" style="padding: 0 0 5px; border-bottom: 1px solid #000000; text-align: left;">DESCRIPTION</th>
                        <th class="table_heading" style="padding: 0 0 5px; border-bottom: 1px solid #000000; text-align: left;">PRICE.</th>
                        <th class="table_heading" style="padding: 0 0 5px; border-bottom: 1px solid #000000; text-align: left;">DISC.</th>
                        <th class="table_heading" style="padding: 0 0 5px; border-bottom: 1px solid #000000; text-align: left;">SUBTOTAL</th>
                    </tr>
                    @foreach($products as $product)
                        <tr style="border-bottom: 1px solid #000000;">
                            <td style="padding: 0 0 5px; margin: 0; border-bottom: 1px solid #000000;">
                                <p style="marging: 0; padding: 0">{{$product->quantity}}</p>
                            </td>
                            <td style="padding: 0 0 5px; margin: 0; border-bottom: 1px solid #000000;">
                                <p style="marging: 0; padding: 0">{{$product->name}}</p>
                            </td>
                            <td style="padding: 0 0 5px; margin: 0; border-bottom: 1px solid #000000;">
                                <p style="marging: 0; padding: 0">{{$product->base_price}}</p>
                            </td>
                            <td style="padding: 0 0 5px; margin: 0; border-bottom: 1px solid #000000;">
                                <p style="marging: 0; padding: 0">{{$product->discount}}</p>
                            </td>
                            <td style="padding: 0 0 5px; margin: 0; border-bottom: 1px solid #000000;">
                                <p style="marging: 0; padding: 0">{{$product->subtotal}}</p>
                            </td>
                        </tr>
                    @endforeach
                </table>
                </div>
                @php
                $vat = $total*(float)$product->vat/100;
                $totals = $total+$vat;
                @endphp
                <div style=" width: 100%;">
                    <table style="border-collapse: collapse; vertical-align: top; width: 100%; padding-top:20px;">
                        <tr>
                            <td>
                                <div>
                                    <table style="border-collapse: collapse; width: 100%; ">
                                        <tr style="border-bottom: 1px solid #000000;">
                                            <th class="table_heading" style="padding: 5px 0; text-align: left;">BASE</th>
                                            <th></th>
                                            <th class="table_heading" style="padding: 5px 0; text-align: right;">$ {{$total}} </th>
                                        </tr>
                                            <tr style="border-bottom: 1px solid #000000;">
                                                <td style="padding: 5px 0;  margin: 0; text-align: left;">{{$total}} </td>
                                                <td style="padding: 5px 0; text-align: center"><span> VAT {{$product->vat}}%</span></td>
                                                <td style="padding: 5px 0; text-align: right">{{ $vat }}</td>
                                            </tr>
                                        <tr>
                                            <th class="table_heading" style="padding: 5px 0; text-align: left">TOTAL</th>
                                            <td style="padding: 0; margin: 0;"></td>
                                            <th style="text-align: right">$ {{$totals}}</th>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

                <div style="margin-top: 30px;">       
                    <table style="border-collapse: collapse; width:100%; ">
                        <tr style=" border-bottom: 1px solid #000000;">
                            <th class="table_heading" style="padding: 0 0 5px; border-bottom: 1px solid #000000; text-align: left; font-size:22px;">Vencimientos:</th>
                        </tr>
                    </table>
                </div>
                <div style="margin-top: 0;">       
                    <table style="border-collapse: collapse; width:100%; ">
                        <tr style=" border-bottom: 1px solid #000000;">
                            <th class="table_heading" style="padding: 0 0 5px; border-bottom: 1px solid #000000; text-align: left;">Date</th>
                            <th class="table_heading" style="padding: 0 0 5px; border-bottom: 1px solid #000000; text-align: left;">Amount</th>
                            <th class="table_heading" style="padding: 0 0 5px; border-bottom: 1px solid #000000; text-align: left;">Paid.</th>
                        </tr>
                        <tr style="border-bottom: 1px solid #000000;">
                            <td style="padding: 0 0 5px; margin: 0; border-bottom: 1px solid #000000;">
                                <p style="marging: 0; padding: 0">{{$invoiceData->date}}</p>
                            </td>
                            <td style="padding: 0 0 5px; margin: 0; border-bottom: 1px solid #000000;">
                                <p style="marging: 0; padding: 0">{{$total}}</p>
                            </td>
                            <td style="padding: 0 0 5px; margin: 0; border-bottom: 1px solid #000000;">
                                <p style="marging: 0; padding: 0">no</p>
                            </td>
                        </tr>
                    </table>
                </div>
                @if($invoiceData->client)
                <div style="margin-top: 30px;">       
                    <table style="border-collapse: collapse; width:100%; ">
                        <tr style=" border-bottom: 1px solid #000000;">
                            <th class="table_heading" style="padding: 0 0 5px; border-bottom: 1px solid #000000; text-align: left; font-size:22px;">DATA OF CLIENTE:</th>
                        </tr>
                        <tr>
                            <span>{{ @$invoiceData->client->legal_name }} ({{@$invoiceData->client->name}})</span><br>
                            <span>{{ @$invoiceData->client->tin }}</span><br>
                            <span>{{ @$invoiceData->client->address }}</span><br>
                            <span>{{ @$invoiceData->client->zip_code }} {{ @$invoiceData->client->city }} {{ @$invoiceData->client->state }} {{ @$invoiceData->client->country }}</span><br>
                        </tr>
                    </table>
                </div>
                @elseif($invoiceData->supplier)
                <div style="margin-top: 30px;">       
                    <table style="border-collapse: collapse; width:100%; ">
                        <tr style=" border-bottom: 1px solid #000000;">
                            <th class="table_heading" style="padding: 0 0 5px; border-bottom: 1px solid #000000; text-align: left; font-size:22px;">DATA OF SUPPLIER</th>
                        </tr>
                        <tr>
                            <span>{{ @$invoiceData->supplier->legal_name }} ({{@$invoiceData->supplier->name}})</span><br>
                            <span>{{ @$invoiceData->supplier->tin }}</span><br>
                            <span>{{ @$invoiceData->supplier->address }}</span><br>
                            <span>{{ @$invoiceData->supplier->zip_code }} {{ @$invoiceData->supplier->city }} {{ @$invoiceData->supplier->state }} {{ @$invoiceData->supplier->country }}</span><br>
                        </tr>
                    </table>
                </div>
                @endif
        </div> 
</div>