@php 
    $periodOptions = ['Year: 2019', '1st Quarter', '2nd Quarter','3rd Quarter','4th Quarter'];
    $routeName = strtolower($title);
    if (strcmp($routeName, "overview") != false) {
        $routeName .= '.index';
    }
@endphp
<button class="btn btn-primary" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="fe fe-calendar pr-3"></i> 
    {{ $periodOptions[$period] }}
</button>
<div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
    @for ($i = 0; $i < count($periodOptions); $i++)
        @if ($i != $period)
            <a class="dropdown-item" href="{{ route($routeName, ['period' => $i]) }}">{{ $periodOptions[$i] }}</a>
        @else
            <span class="dropdown-item font-weight-bold disabled" style="background-color: #467fcf; color: white;">{{ $periodOptions[$i] }}</span>
        @endif
    @endfor
</div>