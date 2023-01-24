<table>
    <thead>
        <tr>
            <th>Reference</th>
            <th>Name</th>
            <th>Purchase Price</th>
        </tr>
    </thead>
    <tbody>
        @foreach($expenseInvestments as $expenseInvestment)
        <tr>
            <td>{{ @$expenseInvestment->reference.''.@$expenseInvestment->reference_number}}</td>
            <td>{{ @$expenseInvestment->name}}</td>
            <td>{{ @$expenseInvestment->price}}</td>
        </tr>
        @endforeach
    </tbody>
</table>