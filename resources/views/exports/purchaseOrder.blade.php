<table>
    <thead>
        <tr>
            <th>Reference</th>
            <th>Date</th>
            <th>Supplier</th>
            <th>Supplier TIN</th>
            <th>Supplier Category</th>
            <th>Supplier Email</th>
            <th>Supplier Phone 1</th>
            <th>Supplier Phone 2</th>
            <th>Status</th>
            <th>Payment Option</th>
            <th>Bank Account</th>
            <th>Created By</th>
            <th>Agent</th>
            <th>Purchase Document Ref.</th>
            <th>Income Tax</th>
            <th>Total</th>
            <th>Total Tax</th>
            <th>Total Amount</th>
            <th>Title</th>
            <th>Sent Date</th>
            <th>Email Sent Date</th>
            <th>Delivery Option</th>
            <th>Currency</th>
            <th>Currency Rate</th>
            <th>Comments</th>
            <th>Private Comments</th>
            <th>Addendum</th>
            <th>Signed</th>
            <th>Signature TIN</th>
        </tr>
    </thead>
    <tbody>
        @foreach($purchaseOrders as $purchaseOrder)
        <tr>
            <td>{{@$purchaseOrder->reference.''.@$purchaseOrder->reference_number}}</td>
            <td>{{@$purchaseOrder->date}}</td>
            <td>{{@$purchaseOrder->supplier->legal_name}}</td>
            <td>{{@$purchaseOrder->supplier->tin}}</td>
            <td>{{@$purchaseOrder->category->name}}</td>
            <td>{{@$purchaseOrder->supplier->email}}</td>
            <td>{{@$purchaseOrder->supplier->phone_1}}</td>
            <td>{{@$purchaseOrder->supplier->phone_2}}</td>
            <td>{{@$purchaseOrder->status}}</td>
            <td>{{@$purchaseOrder->payment_options->name}}</td>
            <td>{{@$purchaseOrder->bank_account}}</td>
            <td>{{@$purchaseOrder->created_by_name}}</td>
            <td>{{@$purchaseOrder->agent_name}}</td>
            <td>{{@$purchaseOrder->purchase_document_ref}}</td>
            <td>{{@$purchaseOrder->amount_income_tax}}</td>
            <td>{{@$purchaseOrder->amount_with_out_vat}}</td>
            <td>{{@$purchaseOrder->amount - @$purchaseOrder->amount_with_out_vat}}</td>
            <td>{{@$purchaseOrder->amount}}</td>
            <td>{{@$purchaseOrder->title}}</td>
            <td>{{@$purchaseOrder->sent_date}}</td>
            <td>{{@$purchaseOrder->email_sent_date}}</td>
            <td>{{@$purchaseOrder->delivery_options->name}}</td>
            <td>{{@$purchaseOrder->currency}}</td>
            <td>{{@$purchaseOrder->currency_rate}}</td>
            <td>{{@$purchaseOrder->comments}}</td>
            <td>{{@$purchaseOrder->private_comments}}</td>
            <td>{{@$purchaseOrder->addendum}}</td>
            <td>{{@$purchaseOrder->signature}}</td>
            <td>{{@$purchaseOrder->tin}}</td>
        </tr>
        @endforeach
    </tbody>
</table>