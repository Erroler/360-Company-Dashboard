@extends('layouts.dashboard')

@section('title', 'VidalTech - Entity ' . ucwords($name))

@section('dashboard_content')
  <div class="page-header">
        <h1 class="h1 page-title text-capitalize">{{ $name }}</h1>
        <h6 class="text-muted mt-auto ml-3">[{{ $type }}]</small>
    </div>
<div class="row row-cards">
    <div class="col">
        <div class="card h-100 pb-4">
            <div class="card-title mb-0">
                <div class="h4 mb-5 pt-5 text-center">Information</div>
            </div>
            <div class="px-5">
                <div class="text-capitalize py-1"><strong class="pr-3">Name:</strong>{{strtolower($info->name)}}</div>
                <div class="text-capitalize py-1"><strong class="pr-3">Tax Id:</strong>{{strtolower($info->companyTaxID)}}</div>
                <div class="text-capitalize py-1"><strong class="pr-3">Currency:</strong>{{strtolower($info->currency)}}</div>
                <hr class="my-3">
                <div class="text-capitalize py-1"><strong class="pr-3">Street:</strong>{{strtolower($info->streetName).', '.strtolower($info->buildingNumber)}}</div>
                <div class="text-capitalize py-1"><strong class="pr-3">Postal Code:</strong>{{strtolower($info->postalZone).' '.strtolower($info->cityName)}}</div>
                <div class="text-capitalize py-1"><strong class="pr-3">Country:</strong>{{strtolower($info->country)}}</div>
                <hr class="my-3">
                <div class="text-capitalize py-1"><strong class="pr-3">Telephone:</strong>{{strtolower($info->telephone)}}</div>
                <div class="py-1"><strong class="pr-3">Email:</strong>{{strtolower($info->electronicMail)}}</div>
                <div class="py-1"><strong class="pr-3">Website:</strong><a href="{{strtolower($info->websiteUrl)}}">{{strtolower($info->websiteUrl)}}</a></div>
            </div>
        </div>
    </div>
    <div class="col">

        @if ($type == 'Consumer')
            @include('partials.card', [
                'title' => 'Total Purchases',
                'value' =>  $info->totalTransactions[0] . ' €', 
                'type' =>''
            ])
            @include('partials.products_table', [
                'title' => 'Top Bought Products', 
                'labels' => ['Name', 'Purchase price', 'Sale price', 'Purchased units'],
                'rows' => $top_inventory,
                'type' => 'sold',
                'period' => 0
            ] )
        @else
            @include('partials.card', [
                'title' => 'Total Sales',
                'value' =>  $info->totalTransactions[0] . ' €', 
                'type' =>''
            ])
            @include('partials.products_table', [
                'title' => 'Top Sold Products', 
                'labels' => ['Name', 'Purchase price', 'Sale price', 'Sold units'],
                'rows' => $top_inventory,
                'type' => 'bought',
                'period' => 0
            ] )
        @endif
    </div>
</div>


@endsection