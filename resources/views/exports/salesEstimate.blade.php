<table>
    <thead>
        <tr>
            <th>Reference</th>
            <th>Date</th>
            <th>Client</th>
            <th>Client TIN</th>
            <th>Client Category</th>
            <th>Client Email</th>
            <th>Client Phone 1</th>
            <th>Client Phone 2</th>
            <th>Status</th>
            <th>Payment Option</th>
            <th>Bank Account</th>
            <th>BIC/SWIFT</th>
            <th>Created By</th>
            <th>Agent</th>
            <th>Primary Tax</th>
            <th>Secondary Tax</th>
            <th>Income Tax</th>
            <th>Total</th>
            <th>Total Tax</th>
            <th>Total Amount</th>
            <th>Title</th>
            <th>Validity Date</th>
            <th>Billing Address</th>
            <th>Delivery Address</th>
            <th>Email Sent Date</th>
            <th>Currency</th>
            <th>Currency Rate</th>
            <th>Comments</th>
            <th>Private Comments</th>
            <th>Addendum</th>
            <th>Signed</th>
            <th>Signature Name</th>
            <th>Signature TIN</th>
        </tr>
    </thead>
    <tbody>
        @foreach($salesEstimates as $salesEstimate)
        <tr>
            <td>{{@$salesEstimate->reference.''.$salesEstimate->reference_number}}</td>
            <td>{{@$salesEstimate->date}}</td>
            <td>{{@$salesEstimate->client->legal_name}}</td>
            <td>{{@$salesEstimate->client->tin}}</td>
            <td>{{@$salesEstimate->client->client_category}}</td>
            <td>{{@$salesEstimate->client->email}}</td>
            <td>{{@$salesEstimate->client->phone_1}}</td>
            <td>{{@$salesEstimate->client->phone_2}}</td>
            <td>{{@$salesEstimate->status}}</td>
            <td>{{@$salesEstimate->payment_options->payment_option}}</td>
            <td>{{@$salesEstimate->client->bank_account_account}}</td>
            <td>{{@$salesEstimate->client->bank_account_bic}}</td>
            <td>{{@$salesEstimate->created_by_name}}</td>
            <td>{{@$salesEstimate->created_by_name}}</td>
            <td>{{(@$salesEstimate->subject_to_income_tax)? 'yes':'no'}}</td>
            <td>{{(@$salesEstimate->subject_to_income_tax)? 'yes':'no'}}</td>
            <td>{{(@$salesEstimate->subject_to_income_tax)? 'yes':'no'}}</td>
            <td>{{@$salesEstimate->amount_with_out_vat}}</td>
            <td>{{@$salesEstimate->amount - @$salesEstimate->amount_with_out_vat}}</td>
            <td>{{@$salesEstimate->amount}}</td>
            <td>{{@$salesEstimate->title}}</td>
            <td>{{@$salesEstimate->valid_until}}</td>
            <td>{{@$salesEstimate->inv_address}}</td>
            <td>{{@$salesEstimate->delivery_address}}</td>
            <td>{{@$salesEstimate->email_sent_date}}</td>
            <td>{{@$salesEstimate->currency}}</td>
            <td>{{@$salesEstimate->currency_rate}}</td>
            <td>{{@$salesEstimate->comments}}</td>
            <td>{{@$salesEstimate->private_comments}}</td>
            <td>{{@$salesEstimate->addendum}}</td>
            <td>{{@$salesEstimate->signature}}</td>
            <td>{{@$salesEstimate->tin}}</td>
        </tr>
        @endforeach
    </tbody>
</table>