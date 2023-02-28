<table>
    <thead>
        <tr>
            <th>Currency:</th>
            @foreach($purchaseSupplierExports as $purchaseSupplierExport)
                <td>{{$purchaseSupplierExport['currency']}}</td>
            @endforeach
        </tr>
        <tr>
            <th>After tax:</th>
            <td>data</td>
        </tr>
        <tr>
            <th>According to:</th>
            @foreach($purchaseSupplierExports as $purchaseSupplierExport)
                <td>{{$purchaseSupplierExport['according']}}</td>
            @endforeach
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
            <th>Supplier Category</th>
            <th>Invoiced</th>
            <th>Paid</th>
            <th>Unpaid</th>
        </tr>
    </thead>
    <tbody>
        @foreach($purchaseSupplierExports as $purchaseSupplierExport)
        <tr>
            <td>{{$purchaseSupplierExport['reference']}}</td>
            <td>{{$purchaseSupplierExport['ruc']}}</td>
            <td>{{$purchaseSupplierExport['name']}}</td>
            <td>{{$purchaseSupplierExport['category']}}</td>
            <td>{{$purchaseSupplierExport['invoiced']}}</td>
            <td>{{$purchaseSupplierExport['paid']}}</td>
            <td>{{$purchaseSupplierExport['unpaid']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>