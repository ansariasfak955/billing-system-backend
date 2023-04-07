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
            <td>{{(@$request->category == 'client_categories') ? 'Client Categories' : 'Clients' }}</td>
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
            @if(@$request->product_id)
                <th>Selected Product:</th>
                <td>{{@$data['selectProduct']}}</td>
            @endif
        </tr>
    </thead>
</table>
<table>
    <thead>
        <tr>
            @if(@$request->category == 'client_categories') 
                <th>Name</th>
                <th>Invoiced</th>
                <th>Paid</th>
                <th>Unpaid</th>
            @else
                <th>Reference</th>
                <th>RUC</th>
                <th>Name</th>
                <th>Client Category</th>
                <th>Invoiced</th>
                <th>Paid</th>
                <th>Unpaid</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach($data as $invoiceClients)
            <tr>
                @if(@$request->category == 'client_categories')
                    <td>{{@$invoiceClients['name']}}</td>
                    <td>{{@$invoiceClients['invoiced']}}</td>
                    <td>{{@$invoiceClients['paid']}}</td>
                    <td>{{@$invoiceClients['Unpaid']}}</td>
                @else
                    <td>{{@$invoiceClients['reference']}}</td>
                    <td>{{@$invoiceClients['ruc']}}</td>
                    <td>{{@$invoiceClients['name']}}</td>
                    <td>{{@$invoiceClients['category']}}</td>
                    <td>{{@$invoiceClients['invoiced']}}</td>
                    <td>{{@$invoiceClients['paid']}}</td>
                    <td>{{@$invoiceClients['Unpaid']}}</td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>