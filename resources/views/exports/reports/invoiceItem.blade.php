<table>
    <thead>
        <tr>
            <th>Currency:</th>
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
            <th>Name</th>
            <th>Product Category</th>
            <th>Units</th>
            <th>Invoiced (before tax)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoiceItemsExports as $invoiceItemsExport)
        <tr>
            <td>{{$invoiceItemsExport['reference']}}</td>
            <td>{{$invoiceItemsExport['name']}}</td>
            <td></td>
            <td>{{$invoiceItemsExport['amount']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>