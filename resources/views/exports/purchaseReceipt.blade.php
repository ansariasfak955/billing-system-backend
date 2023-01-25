<table>
    <thead>
        <tr>
            <th>Reference</th>
            <th>Purchase Document Ref.</th>
            <th>Date</th>
            <th>Supplier Reference</th>
            <th>Supplier</th>
            <th>Concept</th>
            <th>Expiration Date</th>
            <th>Payment Date</th>
            <th>Payment Option</th>
            <th>Supplier Bank Account</th>
            <th>Supplier BIC/SWIFT</th>
            <th>Amount</th>
            <th>Paid</th>
            <th>Paid by</th>
            <th>Bank Account</th>
        </tr>
    </thead>
    <tbody>
        @foreach($purchaseReceipts as $purchaseReceipt)
        <tr>
            <td>{{@$purchaseReceipt->invoice->reference.''.@$purchaseReceipt->invoice->reference_number}}</td>
            <td>{{@$purchaseReceipt->invoice->purchase_document_ref}}</td>
            <td>{{@$purchaseReceipt->date}}</td>
            <td>{{@$purchaseReceipt->supplier->reference.''.@$purchaseReceipt->reference_number}}</td>
            <td>{{@$purchaseReceipt->supplier->legal_name}}</td>
            <td>{{@$purchaseReceipt->concept}}</td>
            <td>{{@$purchaseReceipt->expiration_date}}</td>
            <td>{{@$purchaseReceipt->payment_date}}</td>
            <td>{{@$purchaseReceipt->payment_option_name}}</td>
            <td>{{@$purchaseReceipt->supplier->bank_account_account}}</td>
            <td>{{@$purchaseReceipt->supplier->bank_account_bic}}</td>
            <td>{{@$purchaseReceipt->amount}}</td>
            <td>{{(@$purchaseReceipt->paid)? 'yes':'no'}}</td>
            <td>{{@$purchaseReceipt->paid_by_name}}</td>
            <td>{{@$purchaseReceipt->bank_account}}</td>
        </tr>
        @endforeach
    </tbody>
</table>