<div class="card mb-3">
    <div class="card-body p-3 text-center">
        <div class="h4 mb-3 mt-2 text-muted">{{ $title }}</div>
        <div class="h2 mb-0">{{ $value }}</div>
        <div class="text-right pt-2
            @if($type == "increase") 
                text-green">
                    {{ $scndValue }}
                    <i class="fe fe-chevron-up"></i>
            @elseif($type == "decrease") 
                text-red">
                    {{ $scndValue }}
                    <i class="fe fe-chevron-down"></i>
            @elseif($type == "absolute") 
                text-muted">
                    {{ $scndValue }}
            @elseif($type == "maintain")
            text-muted">
                -
            @else
                invisible">_
                
            @endif
        </div>
    </div>
</div>