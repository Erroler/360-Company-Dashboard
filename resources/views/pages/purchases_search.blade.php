@extends('layouts.dashboard', ['title' => 'Purchases Search'])

@section('title', 'VidalTech - Purchases Search')

@section('dashboard_content')
    <div class="row row-cards">
        @include('partials.data_table', [
            'tableName' => 'purchasesSearch',
            'columns' => ['Supplier','Tax ID','Payable Amount','Gross Value','Tax Amount','Document Date','Due date']
        ])
    </div>
@endsection