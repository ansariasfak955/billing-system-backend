<table>
    <thead>
        <tr>
            <th>Reference</th>
            <th>Name</th>
            <th>Identifier</th>
            <th>Serial Number</th>
            <th>Client Reference</th>
            <th>Client Name</th>
            <th>Address</th>
            <th>City/Town</th>
            <th>State/Province</th>
            <th>Post/Zip Code</th>
            <th>Country</th>
            <th>Brand</th>
            <th>Model</th>
            <th>Subject to Maintenance</th>
            <th>Start of the Warranty</th>
            <th>End of the Warranty</th>
            <th>Description</th>
            <th>Private Comments</th>
        </tr>
    </thead>
    <tbody>
        @foreach($clientAssets as $clientAsset)
        <tr>
            <td>{{@$clientAsset->reference.''.@$clientAsset->reference_number}}</td>
            <td>{{@$clientAsset->name}}</td>
            <td>{{@$clientAsset->identifier}}</td>
            <td>{{@$clientAsset->serial_number}}</td>
            <td>{{@$clientAsset->client->reference.''.@$clientAsset->client->reference_number}}</td>
            <td>{{@$clientAsset->client->legal_name}}</td>
            <td>{{@$clientAsset->address}}</td>
            <td>{{@$clientAsset->client->city}}</td>
            <td>{{@$clientAsset->client->state}}</td>
            <td>{{@$clientAsset->client->zip_code}}</td>
            <td>{{@$clientAsset->client->country}}</td>
            <td>{{@$clientAsset->brand}}</td>
            <td>{{@$clientAsset->model}}</td>
            <td>{{(@$clientAsset->subject_to_maintenance)? 'yes':'no'}}</td>
            <td>{{@$clientAsset->start_of_warranty}}</td>
            <td>{{@$clientAsset->end_of_warranty}}</td>
            <td>{{@$clientAsset->description}}</td>
            <td>{{@$clientAsset->private_comments}}</td>
        </tr>
        @endforeach
    </tbody>
</table>