<table>
    <thead>
        <tr>
            <th>Currency:</th>
            <td>data</td>
        </tr>
        <tr>
            <th>After tax:</th>
            <td>data</td>
        </tr>
        <tr>
            <th>Start Date:</th>
            <td>data</td>
        </tr>
        <tr>
            <th>End Date:</th>
            <td>data</td>
        </tr>
    </thead>
</table>
<table>
    <thead>
        <tr>
            <th>Reference</th>
            <th>Name</th>
            <th>TIN</th>
            <th>Client Category</th>
            <th>Post/Zip Code</th>
            <th>2023/Q1</th>
            <th>2023/Q2</th>
            <th>2023/Q3</th>
            <th>2023/Q4</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoiceByClients as $invoiceByClient)
        <tr>
            <td>{{$invoiceByClient['reference']}}</td>
            <td>{{$invoiceByClient['name']}}</td>
            <td>{{$invoiceByClient['tin']}}</td>
            <td>{{$invoiceByClient['client_category']}}</td>
            <td>{{$invoiceByClient['zip_code']}}</td>
            <td>{{$invoiceByClient['Q1']}}</td>
            <td>{{$invoiceByClient['Q2']}}</td>
            <td>{{$invoiceByClient['Q3']}}</td>
            <td>{{$invoiceByClient['Q4']}}</td>
            <td>{{$invoiceByClient['total']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>