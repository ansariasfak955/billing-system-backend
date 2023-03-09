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
            <th>Name</th>
            <th>Pending</th>
            <th>Refused</th>
            <th>Accepted</th>
            <th>Closed</th>
            <th>Total</th>
            <th>Units</th>
            <th>Amount (before tax)</th>
            <th>Product Category</th>
        </tr>
    </thead>
    <tbody>
        @foreach($itemsSalesExports as $itemsSalesExport)
        <tr>
            <td>{{$itemsSalesExport['reference']}}</td>
            <td>{{$itemsSalesExport['name']}}</td>
            <td>{{$itemsSalesExport['pending']}}</td>
            <td>{{$itemsSalesExport['refused']}}</td>
            <td>{{$itemsSalesExport['accepted']}}</td>
            <td>{{$itemsSalesExport['closed']}}</td>
            <td>{{$itemsSalesExport['total']}}</td>
            <td>{{$itemsSalesExport['units']}}</td>
            <td>{{$itemsSalesExport['amount']}}</td>
            <td>{{$itemsSalesExport['category']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>