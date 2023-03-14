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
            <th>Cash Flow Report:</th>
        </tr>
        <tr>
            <th>Deposits</th>
            <td></td>
        </tr>
        <tr>
            <th>Withdrawals</th>
            <td></td>
        </tr>
        <tr>
            <th>Balance</th>
            <td></td>
        </tr>
    </thead>
</table>
<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Reference</th>
            <th>Client/Supplier</th>
            <th>Employee</th>
            <th>Payment Option</th>
            <th>Amount</th>
            <th>Balance</th>
            <th>Paid</th>
        </tr>
    </thead>
    <tbody>
        @foreach($cashFlowExports as $cashFlowExport)
        <tr>
            <td>{{$cashFlowExport['date']}}</td>
            <td>{{$cashFlowExport['type']}}</td>
            <td>{{$cashFlowExport['reference']}}</td>
            <td>{{$cashFlowExport['client']}}</td>
            <td>{{$cashFlowExport['employee']}}</td>
            <td>{{$cashFlowExport['payment_option']}}</td>
            <td>{{$cashFlowExport['amount']}}</td>
            <td>{{$cashFlowExport['payment_option']}}</td>
            <td>{{($cashFlowExport['paid']) ? 'yes' : 'no'}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<table>
    <thead>
        <tr>
            <th>Deposit Report:</th>
        </tr>
        <tr>
            <th>Invoices</th>
            <td>20</td>
        </tr>
        <tr>
            <th>Account Deposits</th>
            <td>30</td>
        </tr>
    </thead>
</table>
<table>
    <thead>
        <tr>
            <th>Withdrawals Report:</th>
        </tr>
        <tr>
            <th>Refunds</th>
            <td>30</td>
        </tr>
        <tr>
            <th>Purchases</th>
            <td>30</td>
        </tr>
        <tr>
            <th>Tickets and other expenses</th>
            <td>30</td>
        </tr>
        <tr>
            <th>Account Withdrawals</th>
            <td>30</td>
        </tr>
    </thead>
</table>