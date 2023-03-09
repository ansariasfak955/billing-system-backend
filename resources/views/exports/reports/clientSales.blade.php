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
            <th>According to:</th>
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
        <tr>
            <th>Document Type:</th>
            <td>data</td>
        </tr>
    </thead>
</table>
<table>
    <thead>
        <tr>
            <th>Reference</th>
            <th>RUC</th>
            <th>Name</th>
            <th>Client Category</th>
            <th>Pending</th>
            <th>Refused</th>
            <th>Accepted</th>
            <th>Closed</th>
            <th>Total</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($clientSalesExports as $clientSalesExport)
        <tr>
            <td>{{$clientSalesExport['reference']}}</td>
            <td>{{$clientSalesExport['tin']}}</td>
            <td>{{$clientSalesExport['name']}}</td>
            <td>{{$clientSalesExport['category']}}</td>
            <td>{{$clientSalesExport['pending']}}</td>
            <td>{{$clientSalesExport['refused']}}</td>
            <td>{{$clientSalesExport['accepted']}}</td>
            <td>{{$clientSalesExport['closed']}}</td>
            <td>{{$clientSalesExport['total']}}</td>
            <td>{{$clientSalesExport['amount']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>