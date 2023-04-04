<table>
    <thead>
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
            <td>{{$finalData['name']}}</td>
            <td>{{$finalData['pending']}}</td>
            <td>{{$finalData['refused']}}</td>
            <td>{{$finalData['resolved']}}</td>
            <td>{{$finalData['closed']}}</td>
            <td>{{$finalData['total']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>