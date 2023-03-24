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
            <th>Grouped by:</th>
            <td>{{ucwords($request->date_sub_type)}}</td>
        </tr>
    </thead>
</table>
<table>
    <thead>
        <tr>
            <th>Period</th>
            <th>Sales</th>
            <th>Expenses</th>
            <th>Profit</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $singleData)
        <tr>
            <td>{{$singleData['period']}}</td>
            <td>{{$singleData['sales']}}</td>
            <td>{{$singleData['expense']}}</td>
            <td>{{$singleData['profit']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>