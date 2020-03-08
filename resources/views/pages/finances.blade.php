@extends('layouts.dashboard', ['title' => 'Finances', 'period' => $saft_kpis['period']])

@section('title', 'VidalTech - Finances')

@section('dashboard_content')
    <div class="row row-cards">
        <div class="col-12 col-lg-7">
            <div class="row row-cards">
                <div class="px-2 col-6">
                    @include('partials.card', [
                        'title' => 'EBIT', 
                        'value' =>  $saft_kpis['EBIT']['Value'] . ' €', 
                        'scndValue' => $saft_kpis['EBIT']['Difference'] . ' %', 
                        'type' => $saft_kpis['EBIT']['Type']
                    ])
                </div>
                <div class="px-2 col-6 ">
                    @include('partials.card', [
                        'title' => 'EBITDA', 
                        'value' =>  $saft_kpis['EBITDA']['Value'] . ' €', 
                        'scndValue' => $saft_kpis['EBITDA']['Difference'] . ' %', 
                        'type' => $saft_kpis['EBITDA']['Type']
                    ])
                </div>
                <div class="px-2 col-6">
                    @include('partials.card', [
                        'title' => 'Net Profit', 
                        'value' =>  $saft_kpis['Net Profit']['Value'] . ' €', 
                        'scndValue' => $saft_kpis['Net Profit']['Difference'] . ' %', 
                        'type' => $saft_kpis['Net Profit']['Type']
                    ])
                </div>
                <div class="px-2 col-6">
                    @include('partials.card', [
                        'title' => 'Gross Profit Margin', 
                        'value' => $jasmin_kpis['Gross Profit Margin']['Value'] . ' %',  
                        'scndValue' => $jasmin_kpis['Gross Profit Margin']['Difference'] . ' €', 
                        'type' => $jasmin_kpis['Gross Profit Margin']['Type']
                    ])
                </div>
                <div class="px-2 col-6">
                    @include('partials.card', [
                        'title' => 'Accounts Payable', 
                        'value' =>  $saft_kpis['Accounts Payable']['Value'] . ' €', 
                        'scndValue' => $saft_kpis['Accounts Payable']['Difference'] . ' %', 
                        'type' => $saft_kpis['Accounts Payable']['Type']
                    ])
                </div>
                <div class="px-2 col-6">
                    @include('partials.card', [
                        'title' => 'Accounts Receivable', 
                        'value' =>  $saft_kpis['Accounts Receivable']['Value'] . ' €', 
                        'scndValue' => $saft_kpis['Accounts Receivable']['Difference'] . ' %', 
                        'type' => $saft_kpis['Accounts Receivable']['Type']
                    ])
                </div>
                <div class="px-2 col-6">
                    @include('partials.card', [
                        'title' => 'Working Capital', 
                        'value' =>  $saft_kpis['Working Capital']['Value'], 
                        'scndValue' => $saft_kpis['Working Capital']['Difference'] . ' %', 
                        'type' => $saft_kpis['Working Capital']['Type']
                    ])
                </div>
                <div class="px-2 col-6">
                    @include('partials.card', [
                        'title' => 'Financial Autonomy', 
                        'value' =>  $saft_kpis['Financial Autonomy']['Value'], 
                        'scndValue' => $saft_kpis['Financial Autonomy']['Difference'] . ' %', 
                        'type' => $saft_kpis['Financial Autonomy']['Type']
                    ])
                </div>
                <div class="px-2 col-6">
                    @include('partials.card', [
                        'title' => 'Current Ratio', 
                        'value' =>  $saft_kpis['Current Ratio']['Value'], 
                        'scndValue' => $saft_kpis['Current Ratio']['Difference'] . ' %', 
                        'type' => $saft_kpis['Current Ratio']['Type']
                    ])
                </div>
                <div class="px-2 col-6">
                    @include('partials.card', [
                        'title' => 'Earning Power', 
                        'value' =>  $saft_kpis['Earning Power']['Value'] . ' %', 
                        'scndValue' => $saft_kpis['Earning Power']['Difference'] . ' %', 
                        'type' => $saft_kpis['Earning Power']['Type']
                    ])
                </div>
            </div>
            <div class="row row-cards">
                <div class="col-12 px-2 mb-3">
                    @include('partials.one_line_chart', [
                        'title' => 'Net Profit', 
                        'name' => 'netProfitChart',
                        'xLabels' => 'period',
                        'year' => '2019',
                        'data' => $saft_kpis['Net Profit ChartInfo'],
                        'period' => $saft_kpis['period']])
                </div>
                <div class="col-12 px-2">
                    @include('partials.one_line_chart', [
                        'title' => 'Return on Assets', 
                        'name' => 'returnAssetsChart',
                        'xLabels' => 'period',
                        'year' => '2019',
                        'data' => $saft_kpis['Return on Assets ChartInfo'],
                        'period' => $saft_kpis['period']])
                </div>
            </div>
        </div>
        <div class="col-12 col-lg">
            <div class="card mb-4 h-100">
                <div class="card-body p-3">
                    <div class="h4 mb-5 mt-5 text-muted pb-4 text-center">Profit and Loss Statement</div>
                    <table class="table mb-2 pt-5">
                        <thead>
                            <tr>
                                <th>Revenues and Gains</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($saft_kpis['Profit and Loss Statement']['revenues'] as $name => $amount)
                            <tr>
                                <td>{{ $name }}</td>
                                <td class="text-right text-nowrap">{{ number_format($amount, 2) }} €</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td class="font-weight-bold">Total Revenues and Gains</td>
                                <td class="text-right text-nowrap">{{ number_format($saft_kpis['Profit and Loss Statement']['total_revenues'], 2) }} €</td>
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
                            @foreach($saft_kpis['Profit and Loss Statement']['expenses'] as $name => $amount)
                            <tr>
                                <td>{{ $name }}</td>
                                <td class="text-right text-nowrap">{{ number_format(abs($amount), 2) }} €</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td class="font-weight-bold">Total Expenses and Losses</td>
                                <td class="text-right text-nowrap">{{ number_format(abs($saft_kpis['Profit and Loss Statement']['total_expenses']), 2) }} €</td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table mb-2">
                        <tbody>
                            <tr>
                                <td class="font-weight-bold">EBITDA</td>
                                <td class="text-right text-nowrap">{{ number_format($saft_kpis['Profit and Loss Statement']['ebitda'], 2) }} €</td>
                            </tr>
                            <tr>
                                <td>Depreciation and Amortization</td>
                                <td class="text-right text-nowrap">{{ number_format(abs($saft_kpis['Profit and Loss Statement']['depreciation_amortization'])) }} €</td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table mb-2">
                        <tbody>
                            <tr>
                                <td class="font-weight-bold">EBIT</td>
                                <td class="text-right text-nowrap">{{ number_format($saft_kpis['Profit and Loss Statement']['ebit'], 2) }} €</td>
                            </tr>
                            <tr>
                                <td>Interess and Taxes</td>
                                <td class="text-right text-nowrap">{{ number_format(abs($saft_kpis['Profit and Loss Statement']['interest_taxes'])) }} €</td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table mb-2">
                        <tbody>
                            <tr>
                                <td class="font-weight-bold">Net Income</td>
                                <td class="text-right">{{ number_format($saft_kpis['Profit and Loss Statement']['net_income'], 2) }} €</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection