<table>
    <thead>
        <tr>
            <th>Currency:</th>
            <td>USD $ - US Dollar</td>
        </tr>
        <tr>
            <th>After tax:</th>
            <td>{{(@$request->after_tax) ? 'Yes' : 'No'}}</td>
        </tr>
        <tr>
            <th>According to:</th>
            <td>Agent</td>
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
            <th>Name</th>
            @if($request->status)
            <th>pending</th>
            <th>amount</th>
            @elseif($request->status)
            <th>refused</th>
            <th>amount</th>
            @elseif($request->status)
            <th>accepted</th>
            <th>amount</th>
            @elseif($request->status)
            <th>closed</th>
            <th>amount</th>
            @else
            <th>pending</th>
            <th>refused</th>
            <th>accepted</th>
            <th>closed</th>
            <th>total</th>
            <th>amount</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach($data as $finalData)
        <tr>
            <td>{{$finalData['name']}}</td>
            @if($request->status)
            <td>{{$finalData['pending']}}</td>
            <td>{{$finalData['amount']}}</td>
            @elseif($request->status)
            <td>{{$finalData['refused']}}</td>
            <td>{{$finalData['amount']}}</td>
            @elseif($request->status)
            <td>{{$finalData['accepted']}}</td>
            <td>{{$finalData['amount']}}</td>
            @elseif($request->status)
            <td>{{$finalData['closed']}}</td>
            <td>{{$finalData['amount']}}</td>
            @else
            <td>{{$finalData['pending']}}</td>
            <td>{{$finalData['refused']}}</td>
            <td>{{$finalData['accepted']}}</td>
            <td>{{$finalData['closed']}}</td>
            <td>{{$finalData['total']}}</td>
            <td>{{$finalData['amount']}}</td>
            @endif
        </tr>
        @endforeach
    </tbody>
</table>