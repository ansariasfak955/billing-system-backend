<table>
    <thead>
        <tr>
            <th>Start Date:</th>
            <td>data</td>
        </tr>
        <tr>
            <th>End Date:</th>
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
        </tr>
    </thead>
    <tbody>
        @foreach($incidentsAgentsExports as $incidentsAgentsExport)
        <tr>
            <td>{{$incidentsAgentsExport['name']}}</td>
            <td>{{$incidentsAgentsExport['pending']}}</td>
            <td>{{$incidentsAgentsExport['refused']}}</td>
            <td>{{$incidentsAgentsExport['accepted']}}</td>
            <td>{{$incidentsAgentsExport['closed']}}</td>
            <td>{{$incidentsAgentsExport['total']}}</td>
            <td>{{$incidentsAgentsExport['amount']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>