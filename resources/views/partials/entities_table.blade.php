<div class="card h-100">
    <div class="h4 mb-3 pt-5 text-muted text-center">{{ $title }}</div>
    <table class="table card-table">
        <tbody>
            <tr>
                @foreach($labels as $key => $label)
                    @if ($key == 0)
                        <th class="text-capitalize">{{ $label }}</th>
                    @else
                        <th class="text-capitalize text-center">{{ $label }}</th>
                    @endif
                @endforeach
            </tr>
            @foreach($rows as $row)
            
            <tr class="py-4">
                <td class="text-capitalize"><a href="{{ route('entity.'.$type, ['nif' => $row->companyTaxID]) }}">{{strtolower(preg_split('/[-,]/', $row->name)[0])}}</a></td>
                <td class="text-center text-muted text-nowrap">{{$row->companyTaxID}}</td>
                <td class="text-center">
                    <strong>{{number_format($row->totalTransactions[$period], 2, ',', ' ')}} €</strong>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if (count($rows) == 0)
        <div class="h5 mt-3 mb-5 text-center">No data to display</div>
    @endif
</div>