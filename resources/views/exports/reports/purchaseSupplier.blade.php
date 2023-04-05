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
            <th>According to:</th>
            <td>{{(@$request->category == 'supplier_categories') ? 'Supplier Categories' : 'Supplier'}}</td>
        </tr>
        <tr>
            <th>Start Date:</th>
            <td>{{@$request->startDate}}</td>
        </tr>
        <tr>
            <th>End Date:</th>
            <td>{{@$request->endDate}}</td>
        </tr>
    </thead>
</table>
<table>
    <thead>
        <tr>
                <th>Reference</th>
                <th>RUC</th>
                <th>Name</th>
                <th>Supplier Category</th>
                <th>Invoiced</th>
                <th>Paid</th>
                <th>Unpaid</th>
        </tr>
    </thead>
    <tbody>
        @foreach(@$data as $finalData)
            <tr>
                    <td>{{$finalData['reference']}}</td>
                    <td>{{$finalData['ruc']}}</td>
                    <td>{{$finalData['name']}}</td>
                    <td>{{$finalData['category']}}</td>
                    <td>{{$finalData['invoiced']}}</td>
                    <td>{{$finalData['paid']}}</td>
                    <td>{{$finalData['Unpaid']}}</td>
            </tr>
        @endforeach
    </tbody>
</table>