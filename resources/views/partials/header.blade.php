<div class="header py-4">
    <div class="container">
        <div class="d-flex">
            <div class="header-brand">
                <img src="{{ asset('images/logo.png') }}" class="header-brand-img" alt="tabler logo">
            </div>
            <div class="d-flex order-lg-2 ml-auto">
                <div class="nav-link pr-0 leading-none normal-cursor">
                    <span class="avatar" style="background-image: url( {{ asset('images/user.jpg') }})"></span>
                    <span class="ml-2 d-none d-lg-block">
                        <span class="text-default">{{ \Auth::user()->name }}</span>
                        <a href="{{ route('logout') }}" class="d-block mt-1 small d-inline">Logout</a>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>