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
        <tr>
            <th>Document Type:</th>
            <td>{{$request->reference}}</td>
        </tr>
        <tr>
            @if($request->status)
                <th>Document Type:</th>
                <td>{{@$request->status}}</td>
            @endif
        </tr>
    </thead>
</table>
<table>
    <thead>
        <tr>
                @if($request->category == 'clients')
                    <th>Reference</th>
                    <th>RUC</th>
                    <th>Name</th>
                    <th>Client Category</th>
                @endif
                @if($request->status)
                    <th>Pending</th>
                @elseif($request->status)
                    <th>Refused</th>
                @elseif($request->status)
                    <th>Accepted</th>
                @elseif($request->status)
                    <th>Closed</th>
                @else
                    <th>Pending</th>
                    <th>Refused</th>
                    <th>Accepted</th>
                    <th>Closed</th>
                    <th>Total</th>
                    <th>Amount</th>
                @endif
        </tr>
    </thead>
    <tbody>
        @foreach($data as $finatData)
        <tr>
                @if($request->category == 'clients')
                    <td>{{$finatData['reference']}}</td>
                    <td>{{$finatData['tin']}}</td>
                    <td>{{$finatData['name']}}</td>
                    <td>{{@$finatData['category']}}</td>
                @endif
                @if($request->status)
                    <td>{{$finatData['pending']}}</td>
                @elseif($request->status)
                    <td>{{$finatData['refused']}}</td>
                @elseif($request->status)
                    <td>{{$finatData['accepted']}}</td>
                @elseif($request->status)
                    <td>{{$finatData['closed']}}</td>
                @else
                    <td>{{$finatData['pending']}}</td>
                    <td>{{$finatData['refused']}}</td>
                    <td>{{$finatData['accepted']}}</td>
                    <td>{{$finatData['closed']}}</td>
                    <td>{{$finatData['total']}}</td>
                    <td>{{$finatData['amount']}}</td>
                @endif
        </tr>
        @endforeach
    </tbody>
</table>