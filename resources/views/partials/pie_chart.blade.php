<div class="card h-100">
    <div class="pb-5 pt-3 text-center">
        <div class="h4 mb-5 mt-2 text-muted">{{$title}}</div> 
        <div class="d-flex justify-content-center" @if($size == 50) style="min-height:19.5rem" @endif> 
            @if ($data !== "0, 0, 0, 0, 0, 0")
                <canvas id="{{ $name }}" class="h-{{ $size }} w-{{ $size }}"></canvas>
            @else
                <div class="h5 text-center my-auto pb-5">No data to display</div>
            @endif
        </div>
    </div>
</div>
<script>
    var tmp = document.getElementById('{{ $name }}');
    if (tmp != null) {
        var ctx = tmp.getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ('{{ $labels }}').split(", "),
                datasets: [{
                    data: ('{{ $data }}').split(", "),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(255, 206, 86, 0.5)',
                        'rgba(75, 192, 192, 0.5)',
                        'rgba(153, 102, 255, 0.5)',
                        'rgba(255, 159, 0, 0.5)'
                    ],
                    borderWidth: 1
                }]
            }
        });
    }
</script>