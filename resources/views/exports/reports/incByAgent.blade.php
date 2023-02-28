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
            <th>pending</th>
            <th>refused</th>
            <th>accepted</th>
            <th>closed</th>
            <th>total</th>
            <th>amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($incidentByAgentExports as $incidentByAgentExport)
        <tr>
            <td>{{$incidentByAgentExport['name']}}</td>
            <td>{{$incidentByAgentExport['pending']}}</td>
            <td>{{$incidentByAgentExport['refused']}}</td>
            <td>{{$incidentByAgentExport['accepted']}}</td>
            <td>{{$incidentByAgentExport['closed']}}</td>
            <td>{{$incidentByAgentExport['total']}}</td>
            <td>{{$incidentByAgentExport['amount']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>