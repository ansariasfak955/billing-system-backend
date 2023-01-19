<table>
    <thead>
        <tr>
            <td>Concept</td>
            <td>Date</td>
            <td>Payment Option</td>
            <td>Back Account</td>
            <td>Amount</td>
            <td>Paid by</td>
        </tr>
    </thead>
    <tbody>
        @foreach($invoiceDeposits as $invoiceDeposit)
        <tr>
            <td>{{ @$invoiceDeposit->concept }}</td>
            <td>{{ @$invoiceDeposit->date}}</td>
            <td>{{ @$invoiceDeposit->payment_option}}</td>
            <td></td>
            <td>{{ @$invoiceDeposit->amount}}</td>
            <td>{{ @$invoiceDeposit->paid_by}}</td>
        </tr>
        @endforeach
    </tbody>
</table>