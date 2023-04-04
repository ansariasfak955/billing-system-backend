<table>
    <thead>
        <tr>
            <th>Currency:</th>
            <td>USD $ - US Dollar</td>
        </tr>
        <tr>
            <th>After tax:</th>
            <td>{{($request->after_tax) ? 'Yes' : 'No'}}</td>
        </tr>
        <tr>
            <th>According to:</th>
            <td>{{($request->category == 'client_categories') ? 'Client Categories' : 'Clients'}}</td>
        </tr>
        <tr>
            <th>Start Date:</th>
            <td>{{$request->startDate}}</td>
        </tr>
        <tr>
            <th>End Date:</th>
            <td>{{$request->endDate}}</td>
        </tr>
    </thead>
</table>
<table>
    <thead>
        <tr>
            @if($request->category == 'clients') 
                <th>Reference</th>
                <th>RUC</th>
                <th>Name</th>
            @endif
            <th>Client Category</th>
            <th>Invoiced</th>
            <th>Paid</th>
            <th>Unpaid</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $invoiceClients)
            <tr>
                @if(@$request->category == 'clients')
                    <td>{{@$invoiceClients['reference']}}</td>
                    <td>{{@$invoiceClients['ruc']}}</td>
                    <td>{{@$invoiceClients['name']}}</td>
                @endif
            <td>{{@$invoiceClients['category']}}</td>
            <td>{{@$invoiceClients['invoiced']}}</td>
            <td>{{@$invoiceClients['paid']}}</td>
            <td>{{@$invoiceClients['Unpaid']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>