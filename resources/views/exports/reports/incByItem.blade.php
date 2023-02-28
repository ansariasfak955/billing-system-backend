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
        @foreach($incidentByItemExports as $incidentByItemExport)
        <tr>
            <td>{{$incidentByItemExport['reference']}}</td>
            <td>{{$incidentByItemExport['name']}}</td>
            <td>{{$incidentByItemExport['pending']}}</td>
            <td>{{$incidentByItemExport['refused']}}</td>
            <td>{{$incidentByItemExport['accepted']}}</td>
            <td>{{$incidentByItemExport['closed']}}</td>
            <td>{{$incidentByItemExport['total']}}</td>
            <td>{{$incidentByItemExport['units']}}</td>
            <td>{{$incidentByItemExport['amount']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>