<table>
    <thead>
        <tr>
            <th>Reference</th>
            <th>Legal Name</th>
            <th>Name</th>
            <th>TIN</th>
            <th>Phone 1</th>
            <th>Phone 2</th>
            <th>Fax</th>
            <th>Email</th>
            <th>Address</th>
            <th>City/Town</th>
            <th>State/Province</th>
            <th>Post/Zip Code</th>
            <th>Country</th>
            <th>Latitude</th>
            <th>Longitude</th>
            <th>Website</th>
            <th>Category</th>
            <th>Comments</th>
            <th>Payment Option</th>
            <th>Payment terms</th>
            <th>Payment Date</th>
            <th>Adjust Payment</th>
            <th>Discount</th>
            <th>Rate</th>
            <th>Agent</th>
            <th>Invoice To</th>
            <th>Currency</th>
            <th>Income Tax</th>
            <th>Account Name</th>
            <th>Bank Account</th>
            <th>BIC/SWIFT</th>
            <th>Account Description</th>
        </tr>
    </thead>
    <tbody>
        @foreach($suppliers as $supplier)
        <tr>
            <td>{{@$supplier->reference.''.@$supplier->reference_number}}</td>
            <td>{{@$supplier->legal_name}}</td>
            <td>{{@$supplier->name}}</td>
            <td>{{@$supplier->tin}}</td>
            <td>{{@$supplier->phone_1}}</td>
            <td>{{@$supplier->phone_2}}</td>
            <td>{{@$supplier->email}}</td>
            <td>{{@$supplier->address}}</td>
            <td>{{@$supplier->city}}</td>
            <td>{{@$supplier->state}}</td>
            <td>{{@$supplier->zip_code}}</td>
            <td>{{@$supplier->country}}</td>
            <td>{{@$supplier->address_latitude}}</td>
            <td>{{@$supplier->address_longitude}}</td>
            <td>{{@$supplier->website}}</td>
            <td>{{@$supplier->supplier_category_name}}</td>
            <td>{{@$supplier->comments}}</td>
            <td>{{@$supplier->payment_options->name}}</td>
            <td>{{@$supplier->payment_terms->name}}</td>
            <td>{{@$supplier->payment_date}}</td>
            <td>{{@$supplier->payment_adjustment}}</td>
            <td>{{@$supplier->discount}}</td>
            <td>{{@$supplier->rate}}</td>
            <td>{{@$supplier->agent}}</td>
            <td>{{@$supplier->invoice_to}}</td>
            <td>{{@$supplier->currency}}</td>
            <td>{{@$supplier->subject_to_income_tax}}</td>
            <td>{{@$supplier->bank_account_name}}</td>
            <td>{{@$supplier->bank_account_account}}</td>
            <td>{{@$supplier->bank_account_bic}}</td>
            <td>{{@$supplier->bank_account_description}}</td>
        </tr>
        @endforeach
    </tbody>
</table>