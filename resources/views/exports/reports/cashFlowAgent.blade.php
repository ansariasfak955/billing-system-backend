<table>
    <thead>
        <tr>
            <th>Currency:</th>
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
            <th>Showing:</th>
            <td>data</td>
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
        @foreach($cashflowAgentExports as $cashflowAgentExport)
        <tr>
            <td>{{$cashflowAgentExport['name']}}</td>
            <td>{{$cashflowAgentExport['deposit']}}</td>
            <td>{{$cashflowAgentExport['withdrawals']}}</td>
            <td>{{$cashflowAgentExport['balance']}}</td>
            <td></td>
        </tr>
        @endforeach
    </tbody>
</table>