@extends('layouts.dashboard', ['title' => 'Purchases', 'period' => $period])

@section('title', 'VidalTech - Purchases')

@section('dashboard_content')
    <div class="row row-cards">
        <div class="col-12 col-lg-4">
            @include('partials.card', [
                'title' => 'Total Purchases', 
                'value' => $kpis['Total Purchases']['Value'] . ' €',
                'scndValue' => $kpis['Total Purchases']['Difference'] . ' %',
                'type' => $kpis['Total Purchases']['Type']
            ])
            <div class="h-100 mb-5">
            @include('partials.pie_chart', [
                'title' => 'Purchases per product group', 
                'name' => 'purchasesGroupChart',
                'labels' => $purchases_per_product_group_lbls,
                'data' => $purchases_per_product_group_values,
                'size' => 75])
            </div>
        </div>
        <div class="col-12 col-lg">
            @include('partials.entities_table', [
                'title' => 'Top Suppliers', 
                'labels' => ['Name', 'Tax ID', 'Purchases'],
                'rows' => $top_suppliers,
                'type' => 'supplier',
                'period' => $period ])
        </div>
    </div>
    <div class="row row-cards mt-0">
        <div class="col">
            @include('partials.one_line_chart', [
                'title' => 'Purchases', 
                'name' => 'purchasesChart',
                'xLabels' => 'period',
                'year' => '2019',
                'data' => $kpis['Purchases ChartInfo'],
                'period' => $period])
        </div>
    </div>
@endsection