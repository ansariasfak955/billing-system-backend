<table>
    <thead>
        <tr>
            <th>Currency:</th>
            <td>USD $ - US Dollar</td>
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
            <th>Showing:</th>
            <td>{{($request->after_tax ? 'Yes' : 'No')}}</td>
        </tr>
    </thead>
</table>
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Deposits</th>
            <th>Withdrawals</th>
            <th>Balance</th>
        </tr>
    </thead>
    <tbody>
    @foreach($data as $finalData)
        <tr>
            <td>{{$finalData['name']}}</td>
            <td>{{$finalData['deposit']}}</td>
            <td>{{$finalData['withdrawals']}}</td>
            <td>{{$finalData['balance']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>