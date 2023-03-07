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
    </thead>
</table>
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>2023/Q1</th>
            <th>2023/Q2</th>
            <th>2023/Q3</th>
            <th>2023/Q4</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoiceByagents as $invoiceByagent)
        <tr>
            <td>{{$invoiceByagent['name']}}</td>
            <td>{{$invoiceByagent['Q1']}}</td>
            <td>{{$invoiceByagent['Q2']}}</td>
            <td>{{$invoiceByagent['Q3']}}</td>
            <td>{{$invoiceByagent['Q4']}}</td>
            <td>{{$invoiceByagent['total']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>