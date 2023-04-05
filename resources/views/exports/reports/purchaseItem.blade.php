<table>
    <thead>
        <tr>
            <th>Currency:</th>
            <td>USD $ - US Dollar</td>
        </tr>
        <tr>
            <th>According to:</th>
            <td>{{(@$request->category == 'catalog') ? 'Product Categories' : 'Catalog'}}</td>
        </tr>
        <tr>
            <th>Start Date:</th>
            <td>{{@$request->startDate}}</td>
        </tr>
        <tr>
            <th>End Date:</th>
            <td>{{@$request->endDate}}</td>
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
        @foreach($data as $finalData)
        <tr>
            <td>{{$finalData['reference']}}</td>
            <td>{{$finalData['name']}}</td>
            <td>{{$finalData['category']}}</td>
            <td>{{$finalData['units']}}</td>
            <td>{{$finalData['amount']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>