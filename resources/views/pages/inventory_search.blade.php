@extends('layouts.dashboard', ['title' => 'Inventory Search'])

@section('title', 'VidalTech - Inventory Search')

@section('dashboard_content')
    <div class="container">
        @include('partials.data_table', [
            'tableName' => 'inventorySearch',
            'columns' => ['Product','Description','Brand','Price','Stock']
        ])
    </div>
@endsection