<table>
    <thead>
        <tr>
            <th>Currency:</th>
            <td>USD $ - US Dollar</td>
        </tr>
        <tr>
            <th>After tax:</th>
            <td>{{($request->after_tax ? 'Yes' : 'No')}}</td>
        </tr>
        <tr>
            <th>According to:</th>
            <td>Agent</td>
        </tr>
        <tr>
            <th>Start Date:</th>
            <td>{{$request->startDate}}</td>
        </tr>
        <tr>
            <th>End Date:</th>
            <td>{{$request->endDate}}</td>
        </tr>
        <tr>
            <th>Document Type:</th>
            <td>{{$request->referenceType}}</td>
        </tr>
        <tr>
            @if($request->client)
                <th>Selected Client:</th>
                <td>{{@$data['clientName']}}</td>
            @endif
        </tr>
        <tr>
            @if($request->product_id)
                <th>Selected Product:</th>
                <td>{{@$data['productName']}}</td>
            @endif
        </tr>
    </thead>
</table>
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Pending</th>
            <th>Refused</th>
            <th>Accepted</th>
            <th>Closed</th>
            <th>Total</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $finalData)
        <tr>
            <td>{{$finalData['name']}}</td>
            <td>{{$finalData['pending']}}</td>
            <td>{{$finalData['refused']}}</td>
            <td>{{$finalData['accepted']}}</td>
            <td>{{$finalData['closed']}}</td>
            <td>{{$finalData['total']}}</td>
            <td>{{$finalData['amount']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>