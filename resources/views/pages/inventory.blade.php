@extends('layouts.dashboard', ['title' => 'Inventory', 'period' => $period])

@section('title', 'VidalTech - Inventory')

@section('dashboard_content')
<div class="row row-cards">
    <div class="col">
        <div class="row row-cards">
            <div class="col-12">
                @include('partials.card', [
                    'title' => 'Inventory Value', 
                    'value' => $kpis['Inventory Value'] . ' €', 
                    'type' => ''
                ])
            </div>
            <div class="col-12">
                @include('partials.card', [
                    'title' => 'Inventory Turnover', 
                    'value' => $kpis['Inventory Turnover'] . 'x', 
                    'type' => ''
                ])
            </div>
            <div class="col-12">
                @include('partials.card', [
                    'title' => 'Avg. Inventory Period', 
                    'value' => $kpis['Average Inventory Period'] . ' days', 
                    'type' => ''
                ])
                
            </div>
        </div>
    </div>
    <div class="col">
        @include('partials.pie_chart', [
            'title' => 'Inventory by Category', 
            'name' => 'inventoryCategoryChart',
            'labels' => $inventory_per_category_lbls,
            'data' => $inventory_per_category_values,
            'size' => 50])
    </div>
</div>
<div class="row row-cards mt-0">
    <div class="col">
        @include('partials.products_table', [
            'title' => 'Top Selling Products', 
            'labels' => ['Name', 'Purchase price', 'Sale price', 'Sold units'],
            'rows' => $top_selling_products,
            'type' => '',
            'period' => $period
            ] )
    </div>
    <div class="col">
        @include('partials.products_table', [
            'title' => 'Most Profitable Products', 
            'labels' => ['Name', 'Purchase price', 'Sale price', 'Sold units'],
            'rows' => $most_profitable_products ,
            'type' => '',
            'period' => $period
            ] )
    </div>
</div>
@endsection