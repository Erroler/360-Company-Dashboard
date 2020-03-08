<div class="header collapse d-lg-flex p-0" id="headerMenuCollapse">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg order-lg-first">
                <ul class="nav nav-tabs border-0 flex-column flex-lg-row">
                    <li class="nav-item">
                        <a href="{{ route('overview') }}" class="nav-link @if(\Request::is('overview*')) active @endif"><i class="fe fe-bar-chart"></i> Overview</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('finances.index') }}" class="nav-link @if(\Request::is('finances*')) active @endif"><i class="fe fe-activity"></i> Finances</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('sales.index') }}" class="nav-link @if(\Request::is('sales*')) active @endif"><i class="fe fe-truck"></i> Sales</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('purchases.index') }}" class="nav-link @if(\Request::is('purchases*')) active @endif"><i class="fe fe-shopping-cart"></i> Purchases</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('inventory.index') }}" class="nav-link @if(\Request::is('inventory*')) active @endif"><i class="fe fe-box"></i> Inventory</a>
                    </li>
                    {{-- <li class="nav-item">
                        <a href="{{ route('clients.index') }}" class="nav-link @if(\Request::is('clients*')) active @endif"><i class="fe fe-users"></i> Clients</a>
                    </li> --}}
                    <li class="nav-item ml-auto">
                        <a href="{{ route('about.index') }}" class="nav-link @if(\Request::is('about*')) active @endif"><i class="fe fe-home"></i> VidalTech</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>