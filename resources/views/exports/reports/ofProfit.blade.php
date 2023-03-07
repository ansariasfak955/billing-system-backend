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
    </thead>
</table>
<table>
    <thead>
        <tr>
            <th>Period</th>
            <th>Sales</th>
            <th>Expenses</th>
            <th>Profit</th>
        </tr>
    </thead>
    <tbody>
        @foreach($ofProfits as $ofProfit)
        <tr>
            <td>{{$ofProfit['period']}}</td>
            <td>{{$ofProfit['sales']}}</td>
            <td>{{$ofProfit['expense']}}</td>
            <td>{{$ofProfit['profit']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>