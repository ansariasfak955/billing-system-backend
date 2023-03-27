<table>
    <thead>
        <tr>
            <th>Currency:</th>
            <td>USD $ - US Dollar</td>
        </tr>
        <tr>
            <th>After tax:</th>
            <td>data</td>
        </tr>
        <tr>
            <th>According to:</th>
            <td>data</td>
        </tr>
        <tr>
            <th>Start Date:</th>
            <td>data</td>
        </tr>
        <tr>
            <th>End Date:</th>
            <td>data</td>
        </tr>
    </thead>
</table>
<table>
    <thead>
        <tr>
            <th>Reference</th>
            <th>RUC</th>
            <th>Name</th>
            <th>Client Category</th>
            <th>Invoiced</th>
            <th>Paid</th>
            <th>Unpaid</th>
        </tr>
    </thead>
    <tbody>
        <?php

            // return $data; die;
        
        ?>
        @foreach($data as $invoiceClients)
        <tr>
            <td>{{@$invoiceClients['reference']}}</td>
            <td>{{@$invoiceClients['ruc']}}</td>
            <td>{{@$invoiceClients['name']}}</td>
            <td>{{@$invoiceClients['category']}}</td>
            <td>{{@$invoiceClients['invoiced']}}</td>
            <td>{{@$invoiceClients['paid']}}</td>
            <td>{{@$invoiceClients['Unpaid']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>