@extends('layouts.dashboard')

@section('title', 'VidalTech - Overview')

@section('dashboard_content')
<div class="page-header">
    <h1 class="h1 page-title"><a href="{{ route('about.index') }}" class="text-primary"><i
                class="fas fa-backward"></i></a>&nbsp;&nbsp;Balance Sheet</h1>
</div>
<div class="card">
    <div class="card-body">
        <table class="table mb-2">
            <thead>
                <tr>
                    <th>Assets</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>&nbsp;&nbsp;Current Assets</th>
                    <th></th>
                </tr>
                @foreach($balance_sheet['Ativo']['Ativo corrente'] as $ativo => $balanco)
                <tr>
                    <td class="pl-5">&nbsp;&nbsp;&nbsp;&nbsp;{{ $ativo }}</td>
                    <td class="text-right">{{ number_format($balanco, 2) }} €</td>
                </tr>
                @endforeach
                <tr>
                    <th>&nbsp;&nbsp;Non-current Assets</th>
                    <th></th>
                </tr>
                @foreach($balance_sheet['Ativo']['Ativo não corrente'] as $ativo => $balanco)
                <tr>
                    <td class="pl-5">&nbsp;&nbsp;&nbsp;&nbsp;{{ $ativo }}</td>
                    <td class="text-right">{{ number_format($balanco, 2) }} €</td>
                </tr>
                @endforeach
                <tr>
                    <td class="font-weight-bold">&nbsp;&nbsp;Assets Total</td>
                    <td class="text-right">{{ number_format($balance_sheet['Ativo']['Total do Ativo'], 2) }} €</td>
                </tr>
            </tbody>
        </table>
        <table class="table mb-2">
                <thead>
                    <tr>
                        <th>Liabilities and Shareholder's Equity</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>&nbsp;&nbsp;Current Liabilities</th>
                        <th></th>
                    </tr>
                    @foreach($balance_sheet['Capital Próprio e Passivo']['Passivo']['Passivo corrente'] as $passivo => $balanco)
                    <tr>
                        <td class="pl-5">&nbsp;&nbsp;&nbsp;&nbsp;{{ $passivo }}</td>
                        <td class="text-right">{{ number_format($balanco, 2) }} €</td>
                    </tr>
                    @endforeach
                    <tr>
                        <th>&nbsp;&nbsp;Non-current Liabilities</th>
                        <th></th>
                    </tr>
                    @foreach($balance_sheet['Capital Próprio e Passivo']['Passivo']['Passivo não corrente'] as $passivo => $balanco)
                    <tr>
                        <td class="pl-5">&nbsp;&nbsp;&nbsp;&nbsp;{{ $passivo }}</td>
                        <td class="text-right">{{ number_format($balanco, 2) }} €</td>
                    </tr>
                    @endforeach
                    <tr>
                        <th>&nbsp;&nbsp;Shareholder's Equity</th>
                        <th></th>
                    </tr>
                    @foreach($balance_sheet['Capital Próprio e Passivo']['Capital Próprio'] as $capital => $balanco)
                    <tr>
                        <td class="pl-5">&nbsp;&nbsp;&nbsp;&nbsp;{{ $capital }}</td>
                        <td class="text-right">{{ number_format($balanco, 2) }} €</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td class="font-weight-bold">&nbsp;&nbsp;Liabilities and Shareholder's Equity Total</td>
                        <td class="text-right">{{ number_format($balance_sheet['Capital Próprio e Passivo']['Total do Capital Próprio e do Passivo'], 2) }} €</td>
                    </tr>
                </tbody>
            </table>
    </div>
</div>
@endsection