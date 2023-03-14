<table>
    <thead>
        <tr>
            <th>Currency:</th>
            <td>data</td>
        </tr>
        <tr>
            <th>Date:</th>
            <td>data</td>
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
    @foreach($stockValuationExports as $stockValuationExport)
        <tr>
            <td>{{$stockValuationExport['reference']}}</td>
            <td>{{$stockValuationExport['name']}}</td>
            <td>{{$stockValuationExport['stock']}}</td>
            <td>{{$stockValuationExport['sales_stock_value']}}</td>
            <td>{{$stockValuationExport['purchase_stock_value']}}</td>
            <td>{{$stockValuationExport['category']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>