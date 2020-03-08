<div class="card h-100">
    <div class="h4 mb-3 pt-5 text-muted text-center">{{ $title }}</div>
    <table class="table card-table table-vcenter">
        <tbody>
            <tr>
                @foreach($labels as $label)
                    <th class="text-capitalize">{{ $label}}</th>
                @endforeach
            </tr>
            @foreach($rows as $row)
            <tr class="py-4">
                <td class="text-lowercase text-capitalize text-truncate"><a href="{{ route('inventory.product', ['itemKey' => $row->itemKey]) }}">{{strtolower($row->description)}}</a></td>
                <td class="text-center text-muted text-nowrap">{{number_format($row->purchasePrice, 2, ',', ' ')}} €</td>
                <td class="text-center text-muted text-nowrap">{{number_format($row->salePrice, 2, ',', ' ')}} €</td>
                <td class="text-center">
                    @if ($type == 'bought')
                        <strong>{{$row->amountBought[$period]}}</strong>
                    @else 
                        <strong>{{$row->amountSold[$period]}}</strong>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if (count($rows) == 0)
        <div class="h5 mt-3 mb-5 text-center">No data to display</div>
    @endif
</div>