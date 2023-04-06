<table>
    <thead>
    <tr>
            <th>Currency:</th>
            <td>USD $ - US Dollar</td>
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
            <th>Product Category</th>
            @foreach($data['labels'] as $label)
                <th>{{@$label}}</th>
            @endforeach
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['data'] as $finalData)
        <tr>
            <td>{{$finalData['reference']}}</td>
            <td>{{$finalData['name']}}</td>
            <td>{{$finalData['category']}}</td>
            @foreach($finalData['data'] as $datas)
                <td>{{@$datas}}</td>
            @endforeach
            <td>{{$finalData['total']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>