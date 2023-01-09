 @php
  $color= 'orange';
 @endphp
 <style>
        
        .table_heading{
            color: {{ $color }} !important;
        }
        .header_border {
            border-left: 2px solid {{ $color }} !important;
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

        @php
         $watermark_image = 'default_watermark.png';
        @endphp

    <style>
        th, td {
            vertical-align: top;
        }
    </style>

    <div style="position:relative; font-size: 12px;">
        <img src="{{ $watermark_image }}" alt="" style="position: absolute; z-index: -1; opacity: 0.3; top:50%; left: 50%; transform: translate(-50%); width: 600px">
        <div style="margin-top: 0px;height: 45px;">
            <div style="margin-top: 0px;">
                <table style="border-collapse: collapse; width:100%">
                    <tr>
                        <td style="padding: 0; margin: 0;">
                            <img src="{{ $company->logo }}" alt="" srcset="" style="width: 100px; height: 80px; object-fit: cover;">
                        </td>
                        <td class="header_border" >
                            <span>Company Name:</span>
                            <span style="margin-left: 30px;">{{  @$company->commercial_name }}</span> <br>
                            <span>Address:</span><br>
                            <span style="margin-left: 30px;">{{@$company->pincode}} {{@$company->city}} {{@$company->country}} {{@$company->tin}}</span>
                        </td>
                        <td class="header_border">
                                <span style="margin-left: 30px;">Email:</span> 
                                    {{ $company->email}}
                                <br> 
                                <span style="margin-left: 30px;">Website:</span>
                                    {{ $company->website}}
                            <br>  
                                <span style="margin-left: 30px;">Phone:</span>
                                {{ $company->phone}}
                        </td>
                    </tr>
                </table>
            </div>
        </div>

            <div style="text-align: center; margin-top: 70px;">
                <h2>Receipt</h2>
            </div>
        <div style="height:400px">
            <div style="margin-top: 20px;font-size: 13px">
            </div>
        </div>
    </div>
</body>