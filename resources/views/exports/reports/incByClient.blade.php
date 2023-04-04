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
            <td>{{(@$request->category == 'client_categories') ? 'Client Categories' : 'Clients'}}</td>
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
            <td>{{$request->reference}}</td>
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
            @if($request->category == 'Client')
                <th>Reference</th>
                <th>RUC</th>
            @endif
                <th>Name</th>
            @if($request->category == 'Client')
                <th>Client Category</th>
            @endif
            @if($request->status)
                <th>pending</th>
                <th>total</th>
                <th>amount</th>
            @elseif($request->status)
                <th>refused</th>
                <th>total</th>
                <th>amount</th>
            @elseif($request->status)
                <th>accepted</th>
                <th>total</th>
                <th>amount</th>
            @elseif($request->status)
                <th>closed</th>
                <th>total</th>
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
            @if($request->category == 'Client')
                <td>{{$finalData['reference']}}</td>
                <td>{{$finalData['ruc']}}</td>
            @endif
                <td>{{$finalData['name']}}</td>
            @if($request->category == 'Client')
                <td>{{$finalData['category']}}</td>
            @elseif($request->status)
                <td>{{$finalData['pending']}}</td>
                <td>{{$finalData['total']}}</td>
                <td>{{$finalData['amount']}}</td>
            @elseif($request->status)
                <td>{{$finalData['refused']}}</td>
                <td>{{$finalData['total']}}</td>
                <td>{{$finalData['amount']}}</td>
            @elseif($request->status)
                <td>{{$finalData['accepted']}}</td>
                <td>{{$finalData['total']}}</td>
                <td>{{$finalData['amount']}}</td>
            @elseif($request->status)
                <td>{{$finalData['closed']}}</td>
                <td>{{$finalData['total']}}</td>
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