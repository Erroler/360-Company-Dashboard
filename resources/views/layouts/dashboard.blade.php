@extends('layouts.app')

@section('content')
<div class="page">
    <div class="flex-fill">
        @include('partials.header')
        @include('partials.navbar')
        <div class="my-3 my-md-5">
            <div class="container">
                @if(isset($title))
                <div class="page-header justify-content-between">
                    <h1 class="h1 page-title text-capitalize">{{ $title }}</h1>
                    @if($title == 'Inventory')
                        <a href="{{ route('inventory.search') }}" class="btn btn-primary ml-auto mr-3" role="button" aria-pressed="true"><i class="fe fe-search pr-3"></i>Search</a>                   
                    @elseif($title == 'Sales')
                        <a href="{{ route('sales.search') }}" class="btn btn-primary ml-auto mr-3" role="button" aria-pressed="true"><i class="fe fe-search pr-3"></i>Orders</a>                   
                    @elseif($title == 'Purchases')
                        <a href="{{ route('purchases.search') }}" class="btn btn-primary ml-auto mr-3" role="button" aria-pressed="true"><i class="fe fe-search pr-3"></i>Search</a>                   
                    @endif
                    @isset($period)
                        @include('partials.periodButton', ['title' => $title, 'period' => $period])
                    @endisset
                </div>
                @endif
                @yield('dashboard_content')
            </div>
        </div>
    </div>
    @include('partials.footer')
</div>

@endsection