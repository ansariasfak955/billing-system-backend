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
    </thead>
</table>
<table>
    <thead>
        <tr>
            <th>Reference</th>
            <th>RUC</th>
            <th>Name</th>
            <th>Client Category</th>
            <th>Invoiced</th>
            <th>Paid</th>
            <th>Unpaid</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoiceClientsExports as $invoiceClientsExport)
        <tr>
            <td>{{$invoiceClientsExport['reference']}}</td>
            <td>{{$invoiceClientsExport['ruc']}}</td>
            <td>{{$invoiceClientsExport['name']}}</td>
            <td></td>
            <td>{{$invoiceClientsExport['invoiced']}}</td>
            <td>{{$invoiceClientsExport['paid']}}</td>
            <td>{{$invoiceClientsExport['Unpaid']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>