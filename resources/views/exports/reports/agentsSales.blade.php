<table>
    <thead>
        <tr>
            <th>Currency:</th>
            <td>data</td>
        </tr>
        <tr>
            <th>After tax:</th>
            <td>data</td>
        </tr>
        <tr>
            <th>According to:</th>
            <td>data</td>
        </tr>
        <tr>
            <th>Start Date:</th>
            <td>data</td>
        </tr>
        <tr>
            <th>End Date:</th>
            <td>data</td>
        </tr>
        <tr>
            <th>Document Type:</th>
            <td>data</td>
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
        @foreach($agentsSalesExports as $agentsSalesExport)
        <tr>
            <td>{{$agentsSalesExport['legal_name']}}</td>
            <td>{{$agentsSalesExport['pending']}}</td>
            <td>{{$agentsSalesExport['refused']}}</td>
            <td>{{$agentsSalesExport['accepted']}}</td>
            <td>{{$agentsSalesExport['closed']}}</td>
            <td>{{$agentsSalesExport['total']}}</td>
            <td>{{$agentsSalesExport['amount']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>