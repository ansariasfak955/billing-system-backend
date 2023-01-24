<table>
    <thead>
        <tr>
            <th>Reference</th>
            <th>Name</th>
            <th>Base Sales Price</th>
            <th>Promotional</th>
        </tr>
    </thead>
    <tbody>
        @foreach($services as $service)
        <tr>
            <td>{{ @$service->reference.''.@$service->reference_number}}</td>
            <td>{{ @$service->name}}</td>
            <td>{{ @$service->price}}</td>
            <td>{{ (@$service->is_promotional)? 'yes':'no'}}</td>
        </tr>
        @endforeach
    </tbody>
</table>