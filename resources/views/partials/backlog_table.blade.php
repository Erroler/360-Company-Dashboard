<div class="card h-100">
    <div class="h4 mb-3 pt-5 text-muted text-center">{{ $title }}</div>
    <table class="table card-table table-vcenter">
        <tbody>
            <tr>
                @foreach($labels as $label)
                    <th class="text-capitalize text-center">{{ $label}}</th>
                @endforeach
            </tr>
            @foreach($rows as $row)
            <tr class="py-5">
                <td class="text-lowercase text-capitalize text-truncate"><a href="{{ route('entity.consumer', ['nif' => $row->buyerCustomerPartyTaxId]) }}">{{strtolower($row->client)}}</a></td>
                <td class="text-center text-muted text-nowrap">{{$row->documentDate}}</td>
                <td class="text-center text-muted text-nowrap">{{$row->unloadingDate}}</td>
                <td class="text-center text-muted text-nowrap">{{number_format($row->payableAmountAmount, 2, ',', ' ')}} €</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if (count($rows) == 0)
        <div class="h5 mt-3 mb-5 text-center">No data to display</div>
    @endif
</div>