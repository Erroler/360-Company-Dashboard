<div class="card h-100 mb-0 pb-5">
    <div class="card-body pt-3 text-center ">
        <div class="h4 mb-5 mt-3 text-muted">{{$title1}} <small style="color:skyblue">●</small> <small>vs</small> {{$title2}} <small style="color:darkorchid;">●</small></div>      
    <canvas id="{{ $name }}"></canvas>
    </div>
</div>
<script>
    var ctx = document.getElementById('{{ $name }}').getContext('2d');
    var xLabels = [];
    let data1 = '{{ $data1 }}';
    let data2 = '{{ $data2 }}';

    if ('{{ $xLabels }}' === 'year' || {{$period}} == 0){
        xLabels = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 
                    'August', 'September', 'October', 'November', 'Dezember'];
    }
    else if ('{{ $xLabels }}' == 'period'){
        @php 
            $initMonth = $period * 3 - 3;
            $finalMonth = $period * 3;
        @endphp
        xLabelsTmp = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 
                    'August', 'September', 'October', 'November', 'Dezember'];
        xLabels = xLabelsTmp.slice({{$initMonth}}, {{$finalMonth}});
        data1 = data1.split(", ").slice({{$initMonth}}, {{$finalMonth}}).join(", ");
        data2 = data2.split(", ").slice({{$initMonth}}, {{$finalMonth}}).join(", ");

    }
    else {
        let nrDaysinMonth = new Date('{{ $year }}', '{{ $xLabels }}', 0).getDate();
        let i = 1
        while (i <= nrDaysinMonth) {
            xLabels.push(i);
            i++;
        }
    }

    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: xLabels,
            datasets: [{
                data: (data1).split(", "),
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            },
            {
                data: (data2).split(", "),
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 1
            },
            ]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            },
            // so the chart label doesn't appear
            legend: {
                display: false
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem) {
                            return " " + tooltipItem.yLabel;
                    }
                }
            },
            // so the chart line doesn't have curves
            elements: {
                line: {
                    tension: 0.2
                }
            },
            
        },
    });
</script>