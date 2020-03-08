@extends('layouts.dashboard', ['title' => 'Overview', 'period' => $period])

@section('title', 'VidalTech - Overview')

@section('dashboard_content')
    <div class="row row-cards">
        <div class="col-12 col-lg">
            <div class="row">
                <div class="col">
                    @include('partials.card', [
                        'title' => 'Net Sales', 
                        'value' => $sales_kpis['Net Sales']['Value'] . ' €', 
                        'scndValue' => $sales_kpis['Net Sales']['Difference'] . ' %', 
                        'type' => $sales_kpis['Net Sales']['Type'],
                    ])
                </div>
                <div class="col">
                    @include('partials.card', [
                        'title' => 'Cost of Goods Sold', 
                        'value' => $sales_kpis['Cost of Goods Sold']['Value'] . ' €', 
                        'scndValue' => $sales_kpis['Cost of Goods Sold']['Difference'] . ' %', 
                        'type' => $sales_kpis['Cost of Goods Sold']['Type'],
                    ])
                </div>
            </div>
            <div class="row">
                <div class="col">
                    @include('partials.card', [
                        'title' => 'Inventory Value', 
                        'value' => $inventory_kpis['Inventory Value'] . ' €', 
                        'type' => ''
                    ])
                </div>
                <div class="col">
                    @include('partials.card', [
                        'title' => 'Inventory Turnover', 
                        'value' => $inventory_kpis['Inventory Turnover'] . 'x', 
                        'type' => ''
                    ])
                    
                </div>
            </div>
            <div class="row">
                <div class="col">
                    @include('partials.card', [
                        'title' => 'Total Assets', 
                        'value' => $saft_kpis['Total Assets']['Value'] . ' €', 
                        'scndValue' => $saft_kpis['Total Assets']['Difference'] . ' %', 
                        'type' => $saft_kpis['Total Assets']['Type']
                    ])
                </div>
                <div class="col">
                    @include('partials.card', [
                        'title' => 'Financial Autonomy', 
                        'value' =>  $saft_kpis['Financial Autonomy']['Value'], 
                        'scndValue' => $saft_kpis['Financial Autonomy']['Difference'] . ' %', 
                        'type' => $saft_kpis['Financial Autonomy']['Type']
                    ])
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6 mb-3">
            @include('partials.two_line_chart', [
                'title1' => 'Net Sales', 
                'title2' => 'Costs of Goods Sold', 
                'name' => 'netSalesChart',
                'xLabels' => 'period',
                'year' => '2019',
                'data1' => $sales_kpis['Net Sales ChartInfo'],
                'data2' => $sales_kpis['Cost of Goods Sold ChartInfo'],
                'period' => $period])
        </div>
    </div>
    <div class="row row-cards">
        <div class="col-12 col-lg-6">
            @include('partials.products_table', [
                'title' => 'Top Selling Products', 
                'labels' => ['Name', 'Purchase price', 'Sale price', 'Sold units'],
                'rows' => $top_selling_products,
                'type' => '' ,
                'period' => $period
            ])
        </div>
        <div class="col">
            @include('partials.entities_table', [
                'title' => 'Top Consumers', 
                'labels' => ['Name', 'Tax ID', 'Purchases'],
                'rows' => $top_clients,
                'type' => 'consumer',
                'period' => $period ])
        </div>
    </div>
@endsection