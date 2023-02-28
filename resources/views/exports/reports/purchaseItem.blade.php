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
            <th>Selected Agent:</th>
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
        @foreach($purchaseItemExports as $purchaseItemExport)
        <tr>
            <td>{{$purchaseItemExport['reference']}}</td>
            <td>{{$purchaseItemExport['name']}}</td>
            <td>{{$purchaseItemExport['category']}}</td>
            <td>{{$purchaseItemExport['units']}}</td>
            <td>{{$purchaseItemExport['amount']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>