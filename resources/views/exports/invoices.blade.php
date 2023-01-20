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
            <td>Sent</td>
            <td>Billing Address</td>
            <td>Billing Address Twon/City</td>
            <td>Billing Address Post/Zip</td>
            <td>Email Sent Date</td>
            <td>Delivery Option</td>
            <td>Currency</td>
            <td>Currency Rate</td>
            <td>Comments</td>
            <td>Private Comments</td>
            <td>Addendum</td>
            <td>Signature Name</td>
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
            <td>{{@$invoice->sent_date}}</td>
            <td>{{@$invoice->inv_address}}</td>
            <td>{{@$invoice->del_address}}</td>
            <td>{{@$invoice->tin}}</td>
            <td>{{@$invoice->email_sent_date}}</td>
            <td>{{@$invoice->delivery_options->name}}</td>
            <td>{{@$invoice->currency}}</td>
            <td>{{@$invoice->currency_rate}}</td>
            <td>{{@$invoice->comments}}</td>
            <td>{{@$invoice->private_comments}}</td>
            <td>{{@$invoice->addendum}}</td>
            <td>{{@$invoice->signature}}</td>
        </tr>
        @endforeach
    </tbody>
</table>