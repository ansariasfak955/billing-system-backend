<table>
    <thead>
        <tr>
            <th>Reference</th>
            <th>Client</th>
            <th>Client TIN</th>
            <th>Client Email</th>
            <th>Client Phone 1</th>
            <th>Client Phone 2</th>
            <th>Address</th>
            <th>City/Town</th>
            <th>State/Province</th>
            <th>Post/Zip Code</th>
            <th>Country</th>
            <th>Created By</th>
            <th>Date</th>
            <th>Assigned To</th>
            <th>Assignment Date</th>
            <th>Closing Date</th>
            <th>Priority</th>
            <th>Status</th>
            <th>Status Changed By</th>
            <th>Description</th>
            <th>Assets</th>
        </tr>
    </thead>
    <tbody>
        @foreach($incidents as $incident)
        <tr>
            <td>{{@$incident->reference.''.$incident->reference_number}}</td>
            <td>{{@$incident->client->legal_name}}</td>
            <td>{{@$incident->client->tin}}</td>
            <td>{{@$incident->client->email}}</td>
            <td>{{@$incident->client->phone_1}}</td>
            <td>{{@$incident->client->phone_2}}</td>
            <td>{{@$incident->address}}</td>
            <td>{{@$incident->client->city}}</td>
            <td>{{@$incident->client->state}}</td>
            <td>{{@$incident->client->zip_code}}</td>
            <td>{{@$incident->client->country}}</td>
            <td>{{@$incident->assigned_to_name}}</td>
            <td>{{@$incident->date}}</td>
            <td>{{@$incident->assigned_to_name}}</td>
            <td>{{@$incident->assigned_date}}</td>
            <td>{{@$incident->closing_date}}</td>
            <td>{{@$incident->priority}}</td>
            <td>{{@$incident->status}}</td>
            <td>{{@$incident->assigned_to_name}}</td>
            <td>{{@$incident->description}}</td>
            <td>{{@$incident->asset_id}}</td>
        </tr>
        @endforeach
    </tbody>
</table>