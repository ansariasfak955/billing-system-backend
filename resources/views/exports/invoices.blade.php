<table>
    <thead>
        <tr>
            <td>Reference</td>
            <td>Date</td>
            <td>Client</td>
            <td>Client TIN</td>
            <td>Client Category</td>
            <td>Client Email</td>
            <td>Client Phone 1</td>
            <td>Client Phone 2</td>
            <td>Status</td>
            <td>Payment Option</td>
            <td>Bank Account</td>
            <td>Payment Terms</td>
            <td>Created By</td>
            <td>Agent</td>
            <td>Primary</td>
            <td>Secondary Tax</td>
            <td>Total</td>
            <td>Total Tax</td>
            <td>Total Amount</td>
            <td>Paid Amount</td>
            <td>Outstanding Amount</td>
            <td>Title</td>
        </tr>
    </thead>
    <tbody>
        @foreach($invoices as $invoice)
        <tr>
            <td>{{@$invoice->reference.''.@$invoice->reference_number}}</td>
            <td>{{@$invoice->date}}</td>
            <td>{{@$invoice->client->legal_name}}</td>
            <td>{{@$invoice->client->tin}}</td>
            <td></td>
            <td>{{@$invoice->client->email}}</td>
            <td>{{@$invoice->client->phone_1}}</td>
            <td>{{@$invoice->client->phone_2}}</td>
            <td>{{@$invoice->status}}</td>
            <td>{{@$invoice->payment_options->name}}</td>
            <td>{{@$invoice->bank_account}}</td>
            <td>{{@$invoice->payment_terms->name}}</td>
            <td>{{@$invoice->client->email}}</td>
            <td>{{@$invoice->client->email}}</td>
            <td>{{(@$invoice->subject_to_vat)?'yes':'no'}}</td>
            <td>{{(@$invoice->subject_to_income_tax)?'yes':'no'}}</td>
            <td>{{@$invoice->amount_with_out_vat}}</td>
            <td>{{@$invoice->amount - @$invoice->amount_with_out_vat}}</td>
            <td>{{@$invoice->amount}}</td>
            <td>{{@$invoice->amount_paid}}</td>
            <td>{{@$invoice->amount_due}}</td>
            <td>{{@$invoice->title}}</td>
        </tr>
        @endforeach
    </tbody>
</table>