<table>
    <thead>
        <tr>
            <th>Currency:</th>
            <td>USD $ - US Dollar</td>
        </tr>
        <tr>
            <th>Date:</th>
            <td>{{$request->date}}</td>
        </tr>
        <tr>
            <th>Stock:</th>
            <td>{{($request->stock) ? 'Real' : 'Virtual Stock'}}</td>
        </tr>
    </thead>
</table>
<table>
    <thead>
        <tr>
            <th>Reference</th>
            <th>Name</th>
            <th>Stock</th>
            <th>Sales stock value</th>
            <th>Purchase stock value</th>
            <th>Product Category</th>
        </tr>
    </thead>
    <tbody>
    @foreach($data as $finalData)
        <tr>
            <td>{{$finalData['reference']}}</td>
            <td>{{$finalData['name']}}</td>
            <td>{{$finalData['stock']}}</td>
            <td>{{$finalData['sales_stock_value']}}</td>
            <td>{{$finalData['purchase_stock_value']}}</td>
            <td>{{$finalData['category']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>