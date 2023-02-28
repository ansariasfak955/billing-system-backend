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
            <th>Name</th>
            <th>Invoiced</th>
            <th>Paid</th>
            <th>Unpaid</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoiceAgentsExports as $invoiceAgentsExport)
        <tr>
            <td>{{$invoiceAgentsExport['name']}}</td>
            <td>{{$invoiceAgentsExport['invoiced']}}</td>
            <td>{{$invoiceAgentsExport['paid']}}</td>
            <td>{{$invoiceAgentsExport['Unpaid']}}</td>
            <td></td>
        </tr>
        @endforeach
    </tbody>
</table>