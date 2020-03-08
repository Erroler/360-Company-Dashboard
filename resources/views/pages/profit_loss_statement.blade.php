@extends('layouts.dashboard')

@section('title', 'VidalTech - Overview')

@section('dashboard_content')
<div class="page-header">
    <h1 class="h1 page-title"><a href="{{ route('about.index') }}" class="text-primary"><i
                class="fas fa-backward"></i></a>&nbsp;&nbsp;Profit and Loss Statement</h1>
</div>
<div class="card">
    <div class="card-body">
        <table class="table mb-2">
            <thead>
                <tr>
                    <th>Revenues and Gains</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($profit_loss_statement['revenues'] as $name => $amount)
                <tr>
                    <td>{{ $name }}</td>
                    <td class="text-right">{{ number_format($amount, 2) }} €</td>
                </tr>
                @endforeach
                <tr>
                    <td class="font-weight-bold">Total Revenues and Gains</td>
                    <td class="text-right">{{ number_format($profit_loss_statement['total_revenues'], 2) }} €</td>
                </tr>
            </tbody>
        </table>
        <table class="table">
            <thead>
                <tr>
                    <th>Expenses and Losses</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($profit_loss_statement['expenses'] as $name => $amount)
                <tr>
                    <td>{{ $name }}</td>
                    <td class="text-right">{{ number_format(abs($amount), 2) }} €</td>
                </tr>
                @endforeach
                <tr>
                    <td class="font-weight-bold">Total Expenses and Losses</td>
                    <td class="text-right">{{ number_format(abs($profit_loss_statement['total_expenses']), 2) }} €</td>
                </tr>
            </tbody>
        </table>
        <table class="table mb-2">
            <tbody>
                <tr>
                    <td class="font-weight-bold">EBITDA</td>
                    <td class="text-right">{{ number_format($profit_loss_statement['ebitda'], 2) }} €</td>
                </tr>
                <tr>
                    <td>Depreciation and Amortization</td>
                    <td class="text-right">{{ number_format(abs($profit_loss_statement['depreciation_amortization']), 2) }} €</td>
                </tr>
            </tbody>
        </table>
        <table class="table mb-2">
            <tbody>
                <tr>
                    <td class="font-weight-bold">EBIT</td>
                    <td class="text-right">{{ number_format($profit_loss_statement['ebit'], 2) }} €</td>
                </tr>
                <tr>
                    <td>Interess and Taxes</td>
                    <td class="text-right">{{ number_format(abs($profit_loss_statement['interest_taxes']), 2) }} €</td>
                </tr>
            </tbody>
        </table>
        <table class="table mb-2">
            <tbody>
                <tr>
                    <td class="font-weight-bold">Net Income</td>
                    <td class="text-right">{{ number_format($profit_loss_statement['net_income'], 2) }} €</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection