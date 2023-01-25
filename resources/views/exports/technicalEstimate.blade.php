<table>
    <thead>
        <tr>
            <th>Reference</th>
            <th>Date</th>
            <th>Client</th>
            <th>Client TIN</th>
            <th>Client Email</th>
            <th>Client Phone 1</th>
            <th>Client Phone 2</th>
            <th>Status</th>
            <th>Payment Option</th>
            <th>Created By</th>
            <th>Agent</th>
            <th>Income Tax</th>
            <th>Total</th>
            <th>Total Tax</th>
            <th>Total Amount</th>
            <th>Title</th>
            <th>Validity Date</th>
            <th>Billing Address</th>
            <th>Work Address</th>
            <th>Email Sent Date</th>
            <th>Invoice To</th>
            <th>Currency</th>
            <th>Currency Rate</th>
            <th>Comments</th>
            <th>Private Comments</th>
            <th>Addendum</th>
            <th>Signed</th>
        </tr>
    </thead>
    <tbody>
        @foreach($technicalEstimates as $technicalEstimate)
        <tr>
            <td>{{@$technicalEstimate->reference.''.@$technicalEstimate->reference_number}}</td>
            <td>{{@$technicalEstimate->date}}</td>
            <td>{{@$technicalEstimate->client->legal_name}}</td>
            <td>{{@$technicalEstimate->client->tin}}</td>
            <td>{{@$technicalEstimate->client->email}}</td>
            <td>{{@$technicalEstimate->client->phone_1}}</td>
            <td>{{@$technicalEstimate->client->phone_2}}</td>
            <td>{{@$technicalEstimate->status}}</td>
            <td>{{@$technicalEstimate->payment_options->name}}</td>
            <td>{{@$technicalEstimate->created_by_name}}</td>
            <td>{{@$technicalEstimate->agent_name}}</td>
            <td>{{@$technicalEstimate->subject_to_income_tax}}</td>
            <td>{{@$technicalEstimate->amount_with_out_vat}}</td>
            <td>{{@$technicalEstimate->amount - @$technicalEstimate->amount_with_out_vat}}</td>
            <td>{{@$technicalEstimate->amount}}</td>
            <td>{{@$technicalEstimate->title}}</td>
            <td>{{@$technicalEstimate->valid_until}}</td>
            <td>{{@$technicalEstimate->inv_address}}</td>
            <td>{{@$technicalEstimate->work_address}}</td>
            <td>{{@$technicalEstimate->email_sent_date}}</td>
            <td>{{@$technicalEstimate->agent_name}}</td>
            <td>{{@$technicalEstimate->currency}}</td>
            <td>{{@$technicalEstimate->currency_rate}}</td>
            <td>{{@$technicalEstimate->comments}}</td>
            <td>{{@$technicalEstimate->private_comments}}</td>
            <td>{{@$technicalEstimate->addendum}}</td>
            <td>{{@$technicalEstimate->signature}}</td>
        </tr>
        @endforeach
    </tbody>
</table>