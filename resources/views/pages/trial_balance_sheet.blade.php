@extends('layouts.dashboard')

@section('title', 'VidalTech - Overview')

@section('dashboard_content')
<div class="page-header">
    <h1 class="h1 page-title"><a href="{{ route('about.index') }}" class="text-primary"><i class="fas fa-backward"></i></a>&nbsp;&nbsp;Trial Balance Sheet</h1>
</div>
<div class="card">
    <div class="card-body p-3">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th rowspan="2" style="width:18rem">Account</th>
                    <th rowspan="1" colspan="2">Beginning Balance</th>
                    <th rowspan="1" colspan="2">Movements</th>
                    <th rowspan="1" colspan="2">Ending Balance</th>
                </tr>
                <tr>
                    <th rowspan="1">Debit</th>
                    <th rowspan="1">Credit</th>
                    <th rowspan="1">Debit</th>
                    <th rowspan="1">Credit</th>
                    <th rowspan="1">Debit</th>
                    <th rowspan="1">Credit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($trial_balance_sheet['accounts'] as $account)
                    <tr>
                        <td>{{ $account['account_id'] }} - {{ $account['description'] }}</td>
                        <td>{{ number_format($account['opening_debit'], 2) }} €</td>
                        <td>{{ number_format($account['opening_credit'], 2) }} €</td>
                        <td>{{ number_format($account['debit_transactions'], 2) }} €</td> 
                        <td>{{ number_format($account['credit_transactions'], 2) }} €</td> 
                        <td>{{ number_format($account['end_debit'], 2) }} €</td>
                        <td>{{ number_format($account['end_credit'], 2) }} €</td> 
                    </tr>
                @endforeach
                <tr>
                    <td class="font-weight-bold">Total</td>
                    <td>{{ number_format($trial_balance_sheet['total_beginning']['debit'], 2) }} €</td>
                    <td>{{ number_format($trial_balance_sheet['total_beginning']['credit'], 2) }} €</td>
                    <td>{{ number_format($trial_balance_sheet['total_transactions']['debit'], 2) }} €</td> 
                    <td>{{ number_format($trial_balance_sheet['total_transactions']['credit'], 2) }} €</td> 
                    <td>{{ number_format($trial_balance_sheet['total_ending']['debit'], 2) }} €</td>
                    <td>{{ number_format($trial_balance_sheet['total_ending']['credit'], 2) }} €</td> 
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection