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
            <th>Reference</th>
            <th>RUC</th>
            <th>Name</th>
            <th>Client Category</th>
            <th>pending</th>
            <th>refused</th>
            <th>accepted</th>
            <th>closed</th>
            <th>total</th>
            <th>amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($incidentByClientExports as $incidentByClientExport)
        <tr>
            <td>{{$incidentByClientExport['reference']}}</td>
            <td>{{$incidentByClientExport['ruc']}}</td>
            <td>{{$incidentByClientExport['name']}}</td>
            <td>{{$incidentByClientExport['category']}}</td>
            <td>{{$incidentByClientExport['pending']}}</td>
            <td>{{$incidentByClientExport['refused']}}</td>
            <td>{{$incidentByClientExport['accepted']}}</td>
            <td>{{$incidentByClientExport['closed']}}</td>
            <td>{{$incidentByClientExport['total']}}</td>
            <td>{{$incidentByClientExport['amount']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>