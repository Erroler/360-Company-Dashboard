@extends('layouts.app')

@section('title', 'VidalTech - Authentication')

@section('content')
<div class="page">
    <div class="page-center">
        <div class="container">
            <div class="row">
                <div class="col col-login mx-auto">
                    <div class="text-center mb-5">
                        <img src="{{ asset('images/logo.png') }}" class="h-7" alt="">
                        <div class="small text-muted">360&#176; Company Dashboard</div>
                    </div>
                    <form class="card" action="{{ route('login.submit') }}" method="post">
                        @csrf
                        <div class="card-body p-6">
                            <div class="card-title">Login to your account</div>
                            @error('auth')<div class="alert alert-danger font-weight-bold py-2 mt-n2">{{ $message }}</div>@enderror
                            <div class="form-group">
                                <label class="form-label">Email address</label>
                                @error('email')<div class="alert alert-danger font-weight-bold py-2">{{ $message }}</div>@enderror
                                <input type="email" name="email" class="form-control"
                                    aria-describedby="emailHelp" placeholder="Enter email">
                            </div>
                            <div class="form-group">
                                <label class="form-label">
                                    Password
                                </label>
                                @error('password')<div class="alert alert-danger font-weight-bold py-2">{{ $message }}</div>@enderror
                                <input type="password" name="password" class="form-control"
                                    placeholder="Password">
                            </div>
                            <div class="form-group">
                                <label class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="remember_me" />
                                    <span class="custom-control-label">Remember me</span>
                                </label>
                            </div>
                            <div class="form-footer">
                                <button type="submit" class="btn btn-primary btn-block">Sign in</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection