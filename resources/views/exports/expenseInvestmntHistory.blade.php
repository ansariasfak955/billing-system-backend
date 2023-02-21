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
            <th>Unit Price</th>
            <th>Quantity</th>
            <th>Product Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($expenseHistorys as $expenseHistory)
        <tr>
            <td>{{@$expenseHistory['reference']}}</td>
            <td>{{@$expenseHistory['date']}}</td>
            <td>{{@$expenseHistory['title']}}</td>
            <td>{{@$expenseHistory['supplier']}}</td>
            <td>{{@$expenseHistory['supplier_tin']}}</td>
            <td></td>
            <td>{{@$expenseHistory['supplier_email']}}</td>
            <td>{{@$expenseHistory['supplier_phone_1']}}</td>
            <td>{{@$expenseHistory['supplier_phone_2']}}</td>
            <td>{{@$expenseHistory['status']}}</td>
            <td></td>
            <td>{{@$expenseHistory['bank_account']}}</td>
            <td>{{@$expenseHistory['created_by_name']}}</td>
            <td>{{@$expenseHistory['agent_name']}}</td>
            <td>{{@$expenseHistory['income_tax']}}</td>
            <td>{{@$expenseHistory['total']}}</td>
            <td>{{@$expenseHistory['total_tax']}}</td>
            <td>{{@$expenseHistory['amount']}}</td>
            <td>{{@$expenseHistory['supplier_address']}}</td>
            <td>{{@$expenseHistory['supplier_city']}}</td>
            <td>{{@$expenseHistory['supplier_state']}}</td>
            <td>{{@$expenseHistory['supplier_zip_code']}}</td>
            <td>{{@$expenseHistory['supplier_country']}}</td>
            <td>{{@$expenseHistory['currency']}}</td>
            <td>{{@$expenseHistory['currency_rate']}}</td>
            <td>{{@$expenseHistory['comments']}}</td>
            <td>{{@$expenseHistory['private_comments']}}</td>
            <td>{{@$expenseHistory['addendum']}}</td> 
            <td>{{@$expenseHistory['signature']}}</td>
            <td>{{@$expenseHistory['unit_price']}}</td>
            <td>{{@$expenseHistory['total_quantity']}}</td>
            <td>{{@$expenseHistory['product_total']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>