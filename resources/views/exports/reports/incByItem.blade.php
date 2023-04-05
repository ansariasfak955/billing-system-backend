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
            <td>{{@$request->startDate}}</td>
        </tr>
        <tr>
            <th>End Date:</th>
            <td>{{@$request->endDate}}</td>
        </tr>
        <tr>
            <th>Document Type:</th>
            <td>{{$request->referenceType}}</td>
        </tr>
        <tr>
            @if($request->status)
                <th>State:</th>
                <td>{{$request->status}}</td>
            @endif
        </tr>
    </thead>
</table>
<table>
    <thead>
        <tr>
            @if($request->category == 'catalog')
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
            @else
                <th>Name</th>
                <th>Pending</th>
                <th>Refused</th>
                <th>Accepted</th>
                <th>Closed</th>
                <th>Total</th>
                <th>Units</th>
                <th>Amount (before tax)</th>
            @endif
            

        </tr>
    </thead>
    <tbody>
        @foreach($data as $finalData)
        <tr>
            @if($request->category == 'catalog')
                <td>{{$finalData['reference']}}</td>
                <td>{{$finalData['name']}}</td>
                <td>{{$finalData['pending']}}</td>
                <td>{{$finalData['refused']}}</td>
                <td>{{$finalData['accepted']}}</td>
                <td>{{$finalData['closed']}}</td>
                <td>{{$finalData['total']}}</td>
                <td>{{$finalData['units']}}</td>
                <td>{{$finalData['amount']}}</td>
                <td>{{$finalData['category']}}</td>
            @else
                <td>{{$finalData['name']}}</td>
                <td>{{$finalData['pending']}}</td>
                <td>{{$finalData['refused']}}</td>
                <td>{{$finalData['accepted']}}</td>
                <td>{{$finalData['closed']}}</td>
                <td>{{$finalData['total']}}</td>
                <td>{{$finalData['units']}}</td>
                <td>{{$finalData['amount']}}</td>
            @endif
        </tr>
        @endforeach
    </tbody>
</table>