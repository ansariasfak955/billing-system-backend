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
            <td>{{$request->startDate}}</td>
        </tr>
        <tr>
            <th>End Date:</th>
            <td>{{$request->endDate}}</td>
        </tr>
        <tr>
            @if($request->client)
                <th>Selected Client:</th>
                <td>{{@$request->client->reference}}</td>
            @endif
        </tr>
    </thead>
</table>
<table>
    <thead>
        <tr>
            <th>Estimates by State:</th>
        </tr>
        <tr>
            <th>State</th>
            <th>Quantity</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th>Pending</th>
            <td>{{@$data['SPendingQuantity']}}</td>
            <td>{{@$data['SPending']}}</td>
        </tr>
        <tr>
            <th>Refused</th>
            <td>{{@$data['SRefusedQuantity']}}</td>
            <td>{{@$data['SRefused']}}</td>
        </tr>
        <tr>
            <th>Accepted</th>
            <td>{{@$data['SAcceptedQuantity']}}</td>
            <td>{{@$data['SAccepted']}}</td>
        </tr>
        <tr>
            <th>Closed</th>
            <td>{{@$data['SClosedQuantity']}}</td>
            <td>{{@$data['SClosed']}}</td>
        </tr>
    </tbody>
</table>
<table>
    <thead>
        <tr>
            <th>Orders by State:</th>
        </tr>
        <tr>
            <th>State</th>
            <th>Quantity</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th>Pending</th>
            <td>{{@$data['OPendingQuantity']}}</td>
            <td>{{@$data['OPending']}}</td>
        </tr>
        <tr>
            <th>Refused</th>
            <td>{{@$data['ORefusedQuantity']}}</td>
            <td>{{@$data['ORefused']}}</td>
        </tr>
        <tr>
            <th>In Progress</th>
            <td>{{@$data['OProgressQuantity']}}</td>
            <td>{{@$data['OProgress']}}</td>
        </tr>
        <tr>
            <th>Closed</th>
            <td>{{@$data['OClosedQuantity']}}</td>
            <td>{{@$data['OClosed']}}</td>
        </tr>
    </tbody>
</table>
<table>
    <thead>
        <tr>
            <th>Delivery Notes by State:</th>
        </tr>
        <tr>
            <th>State</th>
            <th>Quantity</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th>Pending Invoice</th>
            <td>{{@$data['DPendingInvoiceQuantity']}}</td>
            <td>{{@$data['DPendingInvoice']}}</td>
        </tr>
        <tr>
            <th>In Progress</th>
            <td>{{@$data['DInProgressQuantity']}}</td>
            <td>{{@$data['DInProgress']}}</td>
        </tr>
        <tr>
            <th>Invoiced</th>
            <td>{{@$data['DClosedQuantity']}}</td>
            <td>{{@$data['DClosed']}}</td>
        </tr>
        <tr>
            <th>Closed</th>
            <td>{{@$data['DInvoicedQuantity']}}</td>
            <td>{{@$data['DInvoiced']}}</td>
        </tr>
    </tbody>
</table>