<table>
    <thead>
        <tr>
            <th>According to:</th>
            <td>{{($request->category == 'client_categories') ? 'Client Categories' : 'Clients'}}</td>
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
            @if($request->category == 'clients')
                <th>Reference</th>
                <th>RUC</th>
            @endif
            <th>Name</th>
            <th>Pending</th>
            <th>Refused</th>
            <th>Accepted</th>
            <th>Closed</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $finalData)
        <tr>
            @if($request->category == 'clients')
                <td>{{@$finalData['reference']}}</td>
                <td>{{@$finalData['ruc']}}</td>
            @endif
            <td>{{$finalData['name']}}</td>
            <td>{{$finalData['pending']}}</td>
            <td>{{$finalData['refused']}}</td>
            <td>{{$finalData['accepted']}}</td>
            <td>{{$finalData['closed']}}</td>
            <td>{{$finalData['total']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>