@extends('layouts.dashboard', ['title' => 'Sales', 'period' => $period])

@section('title', 'VidalTech - Sales')

@section('dashboard_content')
    <div class="row row-cards">
        <div class="col-12 col-md">
            @include('partials.card', [
                'title' => 'Net Sales', 
                'value' => $kpis['Net Sales']['Value'] . ' €', 
                'scndValue' => $kpis['Net Sales']['Difference'] . ' %', 
                'type' => $kpis['Net Sales']['Type'],
            ])
        </div>
        <div class="col-12 col-md">
            @include('partials.card', [
                'title' => 'Cost of Goods Sold', 
                'value' => $kpis['Cost of Goods Sold']['Value'] . ' €', 
                'scndValue' => $kpis['Cost of Goods Sold']['Difference'] . ' %', 
                'type' => $kpis['Cost of Goods Sold']['Type'],
            ])
        </div>
        <div class="col-12 col-md">
            @include('partials.card', [
                'title' => 'Gross Profit Margin', 
                'value' => $kpis['Gross Profit Margin']['Value'] . ' %', 
                'scndValue' => $kpis['Gross Profit Margin']['Difference'] . ' €', 
                'type' => $kpis['Gross Profit Margin']['Type']
            ])
        </div>
    </div>
    <div class="row row-cards">
        <div class="col-12 col-lg">
            @include('partials.backlog_table', [
                'title' => 'Sales Order Backlog', 
                'labels' => ['Client', 'Payment Date', 'Delivery Date', 'Value'],
                'rows' => $backlog
            ])
        </div>
        <div class="col-12 col-lg mb-0">
            @include('partials.pie_chart', [
                'title' => 'Sales per product group', 
                'name' => 'salesGroupChart',
                'labels' => $sales_per_product_group_lbls,
                'data' => $sales_per_product_group_values,
                'size' => 75])
        </div>
    </div>
    <div class="row row-cards">
        <div class="col-12 col-lg">
            @include('partials.products_table', [
                'title' => 'Top Selling Products', 
                'labels' => ['Name', 'Purchase price', 'Sale price', 'Sold units'],
                'rows' => $top_selling_products,
                'type' => '' ,
                'period' => $period
            ])
        </div>
        <div class="col-12 col-lg">
            @include('partials.entities_table', [
                'title' => 'Top Consumers', 
                'labels' => ['Name', 'Tax ID', 'Purchases'],
                'rows' => $top_clients,
                'type' => 'consumer',
                'period' => $period ])
        </div>
    </div>
    <div class="row row-cards">
        <div class="col">
            @include('partials.one_line_chart', [
                'title' => 'Sales', 
                'name' => 'salesChart',
                'xLabels' => 'period',
                'year' => '2019',
                'data' => $kpis['Net Sales ChartInfo'],
                'period' => $period])
        </div>
    </div>
@endsection