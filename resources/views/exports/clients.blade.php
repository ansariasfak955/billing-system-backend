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
            <th>Primary Tax</th>
            <th>Secondary Tax</th>
            <th>Income Tax</th>
            <th>Account Name</th>
            <th>Bank Account</th>
            <th>BIC/SWIFT</th>
            <th>Account Description</th>
        </tr>
    </thead>
    <tbody>
        @foreach($clients as $client)
        <tr>
            <td>{{@$client->reference.''.@$client->reference_number}}</td>
            <td>{{@$client->legal_name}}</td>
            <td>{{@$client->name}}</td>
            <td>{{@$client->tin}}</td>
            <td>{{@$client->phone_1}}</td>
            <td>{{@$client->phone_2}}</td>
            <td>{{@$client->fax}}</td>
            <td>{{@$client->email}}</td>
            <td>{{@$client->address}}</td>
            <td>{{@$client->city}}</td>
            <td>{{@$client->state}}</td>
            <td>{{@$client->zip_code}}</td>
            <td>{{@$client->country}}</td>
            <td>{{@$client->address_latitude}}</td>
            <td>{{@$client->address_longitude}}</td>
            <td>{{@$client->website}}</td>
            <td>{{@$client->client_category_name}}</td>
            <td>{{@$client->comments}}</td>
            <td>{{@$client->payment_options->payment_option_id}}</td>
            <td>{{@$client->payment_terms->payment_terms_id}}</td>
            <td>{{@$client->payment_date}}</td>
            <td>{{@$client->payment_adjustment}}</td>
            <td>{{@$client->discount}}</td>
            <td>{{@$client->rate}}</td>
            <td>{{@$client->agent}}</td>
            <td>{{@$client->invoice_to}}</td>
            <td>{{@$client->currency}}</td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{@$client->bank_account_name}}</td>
            <td>{{@$client->bank_account_account}}</td>
            <td>{{@$client->bank_account_bic}}</td>
            <td>{{@$client->bank_account_description}}</td>
        </tr>
        @endforeach
    </tbody>
</table>