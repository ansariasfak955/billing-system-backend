<table>
    <thead>
        <tr>
            <th>Currency:</th>
            <td>USD $ - US Dollar</td>
        </tr>
        <tr>
            <th>Start Date:</th>
            <td>{{$request->startDate}}</td>
        </tr>
        <tr>
            <th>End Date:</th>
            <td>{{$request->endDate}}</td>
        </tr>
        <tr>
            <th>Showing:</th>
            <td>{{($request->after_tax ? 'Yes' : 'No')}}</td>
        </tr>
        <tr>
            @if($request->paymentOption)
                <th>Selected Payment Option:</th>
                <td>{{$request->paymentOption}}</td>
            @endif
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
            <td>{{@$overview['Deposits']}}</td>
        </tr>
        <tr>
            <th>Withdrawals</th>
            <td>{{@$overview['Withdrawals']}}</td>
        </tr>
        <tr>
            <th>Balance</th>
            <td>{{@$overview['Balance']}}</td>
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
        @foreach($data as $finalData)
        <tr>
            <td>{{@$finalData['date']}}</td>
            <td>{{@$finalData['type']}}</td>
            <td>{{@$finalData['reference']}}</td>
            <td>{{@$finalData['client']}}</td>
            <td>{{@$finalData['employee']}}</td>
            <td>{{@$finalData['payment_option']}}</td>
            <td>{{@$finalData['amount']}}</td>
            <td>{{@$finalData['payment_option']}}</td>
            <td>{{(@$finalData['paid']) ? 'yes' : 'no'}}</td>
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
            <td>{{@$overview['Invoices']}}</td>
        </tr>
        <tr>
            <th>Account Deposits</th>
            <td>{{@$overview['account_deposits']}}</td>
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
            <td>{{@$overview['Refunds']}}</td>
        </tr>
        <tr>
            <th>Purchases</th>
            <td>{{@$overview['Purchases']}}</td>
        </tr>
        <tr>
            <th>Tickets and other expenses</th>
            <td>{{@$overview['Tickets_expenses']}}</td>
        </tr>
        <tr>
            <th>Account Withdrawals</th>
            <td>{{@$overview['Account_withdrawals']}}</td>
        </tr>
    </thead>
</table>