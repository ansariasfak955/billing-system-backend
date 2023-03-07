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
            <th>Product Category</th>
            <th>2023/Q1</th>
            <th>2023/Q2</th>
            <th>2023/Q3</th>
            <th>2023/Q4</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($purchaseByItems as $purchaseByItem)
        <tr>
            <td>{{$purchaseByItem['reference']}}</td>
            <td>{{$purchaseByItem['name']}}</td>
            <td>{{$purchaseByItem['product_category']}}</td>
            <td>{{$purchaseByItem['Q1']}}</td>
            <td>{{$purchaseByItem['Q2']}}</td>
            <td>{{$purchaseByItem['Q3']}}</td>
            <td>{{$purchaseByItem['Q4']}}</td>
            <td>{{$purchaseByItem['total']}}</td>
        </tr>
        @endforeach
    </tbody>
</table>