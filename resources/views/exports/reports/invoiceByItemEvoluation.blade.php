<table>
    <thead>
        <tr>
            <th>Currency:</th>
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
            <th>Name</th>
            <th>Supplier Category</th>
            <th>2023/Q1</th>
            <th>2023/Q2</th>
            <th>2023/Q3</th>
            <th>2023/Q4</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($purchaseByProviders as $purchaseByProvider)
        <tr>
            <td>{{$purchaseByProvider['reference']}}</td>
            <td>{{$purchaseByProvider['name']}}</td>
            <td>{{$purchaseByProvider['supplier_category']}}</td>
            <td>{{$purchaseByProvider['Q1']}}</td>
            <td>{{$purchaseByProvider['Q2']}}</td>
            <td>{{$purchaseByProvider['Q3']}}</td>
            <td>{{$purchaseByProvider['Q4']}}</td>
            <td>{{$purchaseByProvider['total']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>