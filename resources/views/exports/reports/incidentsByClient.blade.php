<table>
    <thead>
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
    </thead>
</table>
<table>
    <thead>
        <tr>
            <th>Reference</th>
            <th>RUC</th>
            <th>Name</th>
            <th>Pending</th>
            <th>Refused</th>
            <th>Accepted</th>
            <th>Closed</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($incidentByClientExports as $incidentByClientExport)
        <tr>
            <td>{{$incidentByClientExport['reference']}}</td>
            <td>{{$incidentByClientExport['ruc']}}</td>
            <td>{{$incidentByClientExport['name']}}</td>
            <td>{{$incidentByClientExport['pending']}}</td>
            <td>{{$incidentByClientExport['refused']}}</td>
            <td>{{$incidentByClientExport['accepted']}}</td>
            <td>{{$incidentByClientExport['closed']}}</td>
            <td>{{$incidentByClientExport['total']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>