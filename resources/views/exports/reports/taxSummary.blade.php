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
    </thead>
</table>
<table>
    <thead>
    </thead>
    <tbody>
        @foreach($data as $finalData)
            <tr>
                <th>{{$finalData['vat']}}</th>
                <th>Collected</th>
                <th>Paid</th>
                <th>Total</th>
            </tr>
           
            <tr>
                <th>Subtotal</th>
                <td>{{$finalData['collected']}}</td>
                <td>{{$finalData['paid']}}</td>
                <td>{{$finalData['total']}}</td>
            </tr>
            <tr>
                <th>Tax</th>
                <td>{{$finalData['ctax']}}</td>
                <td>{{$finalData['ptax']}}</td>
                <td>{{$finalData['ttax']}}</td>
            </tr>
            <tr></tr>
        @endforeach
    </tbody>
</table>