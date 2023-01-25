<table>
    <thead>
        <tr>
            <th>Reference</th>
            <th>Date</th>
            <th>Client Reference</th>
            <th>Client</th>
            <th>Concept</th>
            <th>Expiration Date</th>
            <th>Payment Date</th>
            <th>Payment Option</th>
            <th>Client Bank Account</th>
            <th>Client BIC/SWIFT</th>
            <th>Amount</th>
            <th>Paid</th>
            <th>Paid by</th>
            <th>Bank Account</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoiceReceipts as $invoiceReceipt)
        <tr>
            <td>{{@$invoiceReceipt->invoice->reference.''.@$invoiceReceipt->invoice->reference_number}}</td>
            <td>{{@$invoiceReceipt->invoice->date}}</td>
            <td>{{@$invoiceReceipt->client->reference.''.@$invoiceReceipt->client->reference_number}}</td>
            <td>{{@$invoiceReceipt->client->legal_name}}</td>
            <td>{{@$invoiceReceipt->concept}}</td>
            <td>{{@$invoiceReceipt->expiration_date}}</td>
            <td>{{@$invoiceReceipt->payment_date}}</td>
            <td>{{@$invoiceReceipt->payment_options->name}}</td>
            <td>{{@$invoiceReceipt->client->bank_account_account}}</td>
            <td>{{@$invoiceReceipt->client->bank_account_bic}}</td>
            <td>{{@$invoiceReceipt->amount}}</td>
            <td>{{(@$invoiceReceipt->paid)? 'yes':'no'}}</td>
            <td>{{@$invoiceReceipt->paid_by}}</td>
            <td>{{@$invoiceReceipt->bank_account}}</td>
        </tr>
        @endforeach
    </tbody>
</table>