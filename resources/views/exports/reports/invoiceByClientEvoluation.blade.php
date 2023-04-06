<table>
    <thead>
        <tr>
            <th>Currency:</th>
            <td>USD $ - US Dollar</td>
        </tr>
        <tr>
            <th>After tax:</th>
            <td>{{(@$request->after_tax) ? 'Yes' : 'No'}}</td>
        </tr>
        <tr>
            <th>Start Date:</th>
            <td>{{@$request->startDate}}</td>
        </tr>
        <tr>
            <th>End Date:</th>
            <td>{{@$request->endDate}}</td>
        </tr>
        <tr>
            <th>Grouped by:</th>
            <td>{{ucwords($request->date_sub_type)}}</td>
        </tr>
    </thead>
</table>
<table>
    <thead>
        <tr>
            <th>Reference</th>
            <th>Name</th>
            <th>TIN</th>
            <th>Client Category</th>
            <th>Post/Zip Code</th>
            @foreach($data['labels'] as $label)
            <th>{{@$label}}</th>
            @endforeach
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['data'] as $finalData)
            
        <tr>
            <td>{{@$finalData['reference']}}</td>
            <td>{{@$finalData['name']}}</td>
            <td>{{@$finalData['tin']}}</td>
            <td>{{@$finalData['client_category']}}</td>
            <td>{{@$finalData['zip_code']}}</td>
            @foreach($finalData['data'] as $datas)
                <td>{{@$datas}}</td>
            @endforeach
            <td>{{@$finalData['total']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>