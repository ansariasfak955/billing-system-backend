<table>
    <thead>
        <tr>
            <td>Reference</td>
            <td>Name</td>
            <td>Base Sales Price</td>
            <td>Promotional</td>
            <td>Stock Almacen Principal</td>
            <td>Virtual Stock Almacen Principal</td>
            <td>Minimum Stock Almacen Principal</td>
            <td>Location Almacen Principal</td>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $product)
        <tr>
            <td>{{ @$product->reference.''.@$product->reference_number}}</td>
            <td>{{ @$product->name}}</td>
            <td>{{ @$product->price}}</td>
            <td>{{ (@$product->is_promotional)? 'yes':'no' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>