<table>
    <thead>
        <tr>
            <th>Currency:</th>
            <td>USD $ - US Dollar</td>
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
            <th>Profit Report (before tax):</th>
        </tr>
        <tr>
            <th>Sales</th>
            <td>{{@$data['Sales']}}</td>
        </tr>
        <tr>
            <th>Expenses</th>
            <td>{{@$data['Expenses']}}</td>
        </tr>
        <tr>
            <th>Profit</th>
            <td>{{@$data['Profit']}}</td>
        </tr>

    </thead>
</table>
<table>
    <thead>
        <tr>
            <th>Sales Report:</th>
        </tr>
        <tr>
            <th>Invoiced</th>
            <td>{{@$data['Invoiced']}}</td>
        </tr>
        <tr>
            <th>Paid</th>
            <td>{{@$data['Paid']}}</td>
        </tr>
        <tr>
            <th>Unpaid</th>
            <td>{{@$data['Unpaid']}}</td>
        </tr>
    </thead>
</table>
<table>
    <thead>
        <tr>
             <th>Purchasing Report:</th>
        </tr>
        <tr>
            <th>Invoiced</th>
            <td>{{@$data['PInvoiced']}}</td>
        </tr>
        <tr>
            <th>Paid</th>
            <td>{{@$data['PPaid']}}</td>
        </tr>
        <tr>
            <th>Unpaid</th>
            <td>{{@$data['PUnpaid']}}</td>
        </tr>
    </thead>
</table>