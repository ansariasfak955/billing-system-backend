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
    </thead>
</table>
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Invoiced</th>
            <th>Paid</th>
            <th>Unpaid</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $finalData)
        <tr>
            <td>{{$finalData['name']}}</td>
            <td>{{$finalData['invoiced']}}</td>
            <td>{{$finalData['paid']}}</td>
            <td>{{$finalData['Unpaid']}}</td>
            <td></td>
        </tr>
        @endforeach
    </tbody>
</table>