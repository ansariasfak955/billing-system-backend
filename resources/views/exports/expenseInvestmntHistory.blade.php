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
            <th>Bank Account</th>
            <th>BIC/SWIFT</th>
            <th>Created By</th>
            <th>Agent</th>
            <th>Primary Tax</th>
            <th>Secondary Tax</th>
            <th>Secondary Tax</th>
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
            <th>Unit price</th>
            <th>Quantity</th>
            <th>Product total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($expenseHistorys as $expenseHistory)
        <tr>
            <td>{{$expenseHistory->reference.''.$expenseHistory->reference_number}}</td>
        </tr>
        @endforeach
    </tbody>
</table>