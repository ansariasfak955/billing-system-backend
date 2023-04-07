<table>
    <thead>
        <tr>
            <th>Currency:</th>
            <td>USD $ - US Dollar</td>
        </tr>
        <tr>
            <th>According to:</th>
            <td>{{($request->category == 'catalog') ? 'Catalog' : 'Product Categories'}}</td>
        </tr>
        <tr>
            <th>Start Date:</th>
            <td>{{$request->startDate}}</td>
        </tr>
        <tr>
            <th>End Date:</th>
            <td>{{$request->endDate}}</td>
        </tr>
    </thead>
</table>
<table>
    <thead>
        <tr>
        @if($request->category == 'catalog')
            <th>Reference</th>
            <th>Name</th>
            <th>Product Category</th>
            <th>Units</th>
            <th>Invoiced (before tax)</th>
        @else
            <th>Name</th>
            <th>Invoiced (before tax)</th>
        @endif
            
        </tr>
    </thead>
    <tbody>
        @foreach($data as $finalData)
        <tr>
            @if($request->category == 'catalog')
                <td>{{$finalData['reference']}}</td>
                <td>{{$finalData['name']}}</td>
                <td>{{$finalData['category']}}</td>
                <td>{{$finalData['units']}}</td>
                <td>{{$finalData['amount']}}</td>
            @else
                <td>{{$finalData['name']}}</td>
                <td>{{$finalData['amount']}}</td>
            @endif

        </tr>
        @endforeach
    </tbody>
</table>