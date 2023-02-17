<table>
    <thead>
        <tr>
            <th>Reference</th>
            <th>Date</th>
            <th>Title</th>
            <th>Supplier</th>
            <th>Supplier TIN</th>
            <th>Supplier Category</th>
            <th>Supplier Email</th>
            <th>Supplier Phone 1</th>
            <th>Supplier Phone 2</th>
            <th>Status</th>
            <th>Payment Option</th>
            <th>Bank Account</th>>
            <th>Created By</th>
            <th>Agent</th>
            <th>Income Tax</th>
            <th>Total</th>
            <th>Total Tax</th>
            <th>Total Amount</th>
            <th>Billing Address</th>
            <th>Billing Address Town/City</th>
            <th>Billing Address Country</th>
            <th>Delivery Address</th>
            <th>Email Sent Date</th>
            <th>Currency</th>
            <th>Currency Rate</th>
            <th>Comments</th>
            <th>Private Comments</th>
            <th>Addendum</th>
            <th>Signed</th>
            <th>Quantity</th>
        </tr>
    </thead>
    <tbody>
        @foreach($expenseHistorys as $expenseHistory)
        <tr>
            <td>{{@$expenseHistory->reference.''.@$expenseHistory->reference_number}}</td>
            <td>{{@$expenseHistory->date}}</td>
            <td>{{@$expenseHistory->title}}</td>
            <td>{{@$expenseHistory->supplier->legal_name}}</td>
            <td>{{@$expenseHistory->supplier->tin}}</td>
            <td>{{@$expenseHistory->category->name}}</td>
            <td>{{@$expenseHistory->supplier->email}}</td>
            <td>{{@$expenseHistory->supplier->phone_1}}</td>
            <td>{{@$expenseHistory->supplier->phone_2}}</td>
            <td>{{@$expenseHistory->status}}</td>
            <td>{{@$expenseHistory->payment_options->name}}</td>
            <td>{{@$expenseHistory->bank_account}}</td>
            <td>{{@$expenseHistory->created_by_name}}</td>
            <td>{{@$expenseHistory->agent_name}}</td>
            <td>{{@$expenseHistory->amount_income_tax}}</td>
            <td>{{@$expenseHistory->amount_with_out_vat}}</td>
            <td>{{@$expenseHistory->amount - @$expenseHistory->amount_with_out_vat}}</td>
            <td>{{@$expenseHistory->amount}}</td>
            <td>{{@$expenseHistory->supplier->address}}</td>
            <td>{{@$expenseHistory->supplier->city}}</td>
            <td>{{@$expenseHistory->supplier->state}}</td>
            <td>{{@$expenseHistory->supplier->zip_code}}</td>
            <td>{{@$expenseHistory->supplier->country}}</td>
            <td>{{@$expenseHistory->currency}}</td>
            <td>{{@$expenseHistory->currency_rate}}</td>
            <td>{{@$expenseHistory->comments}}</td>
            <td>{{@$expenseHistory->private_comments}}</td>
            <td>{{@$expenseHistory->addendum}}</td>
            <td>{{@$expenseHistory->signature}}</td>
            <td>{{@$expenseHistory->total_quantity}}</td>
        </tr>
        @endforeach
    </tbody>
</table>