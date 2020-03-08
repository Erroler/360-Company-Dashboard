@extends('layouts.dashboard')

@section('title', 'VidalTech - Overview')

@section('dashboard_content')
<div class="page-header">
    <h1 class="h1 page-title">VidalTech</h1>
</div>
<div class="card">
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Company Name</dt>
            <dd class="col-sm-9">{{ $about['company_name'] }}</dd>

            <dt class="col-sm-3">Tax Registration Number</dt>
            <dd class="col-sm-9">{{ $about['vat_number'] }}</dd>

            <dt class="col-sm-3">Address</dt>
            <dd class="col-sm-9">{{ $about['address']['street_name'] }}, {{ $about['address']['postal_code'] }} {{ $about['address']['city'] }}, {{ $about['address']['country'] }}</dd>

            <dt class="col-sm-3">Fiscal Year</dt>
            <dd class="col-sm-9">From {{ $about['fiscal_year_start_date'] }} to {{ $about['fiscal_year_end_date'] }}</dd>
        </dl>
        <a class="btn btn-primary" href="{{ route('about.balance_sheet') }}">View balance sheet </a>
        <br>
        <a class="btn btn-primary mt-2" href="{{ route('about.trial_balance_sheet') }}">View trial balance sheet </a>
        <br>
        <a class="btn btn-primary mt-2" href="{{ route('about.profit_loss_statement') }}">View profit and loss statement </a>
    </div>
</div>
@endsection