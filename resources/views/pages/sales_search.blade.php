@extends('layouts.dashboard', ['title' => 'Orders Search'])

@section('title', 'VidalTech - Orders Search')

@section('dashboard_content')
    <div class="row row-cards">
        @include('partials.data_table', [
            'tableName' => 'salesSearch',
            'columns' => ['Buyer','Tax ID','Payable Amount','Gross Value','Tax Amount','Document Date','Due Date']
        ])
    </div>
@endsection