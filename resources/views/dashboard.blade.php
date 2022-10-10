<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">


    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
    </style>
</head>

<body class="antialiased">
    @include('sidemenu')
    <div class="wrapAll">
        <div id="repart2">
            <div>
                <canvas id="AtvChart"></canvas>
            </div>
            <div>
                <canvas id="AlunosCursos"></canvas>
            </div>


        </div>

        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
        <!-- CHARTS -->
        <script>
            var ctx = document.getElementById('AtvChart').getContext('2d');
            var chart = new Chart(ctx, {
                // The type of chart we want to create
                type: 'bar',
                // The data for our dataset
                data: {
                    labels: {!! json_encode($chart->labels) !!},
                    datasets: [{
                            label: 'Atividades totais',
                            backgroundColor: {!! json_encode($chart->colours) !!},
                            borderColor: {!! json_encode($chart->borderColours) !!},
                            borderWidth: 1,
                            data: {!! json_encode($chart->dataset['Total']) !!}
                        },
                        {
                            label: 'atividades abertas',
                            backgroundColor: {!! json_encode($chart->colours) !!},
                            borderColor: {!! json_encode($chart->borderColours) !!},
                            borderWidth: 1,
                            data: {!! json_encode($chart->dataset['Abertas']) !!}
                        },
                        {
                            label: 'atividades fechadas',
                            backgroundColor: {!! json_encode($chart->colours) !!},
                            borderColor: {!! json_encode($chart->borderColours) !!},
                            borderWidth: 1,
                            data: {!! json_encode($chart->dataset['Fechadas']) !!}
                        },
                        {
                            label: 'atividades em andamento',
                            backgroundColor: {!! json_encode($chart->colours) !!},
                            borderColor: {!! json_encode($chart->borderColours) !!},
                            borderWidth: 1,
                            data: {!! json_encode($chart->dataset['Em andamento']) !!}
                        },


                    ]
                },
                // Configuration options go here
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                callback: function(value) {
                                    if (value % 1 === 0) {
                                        return value;
                                    }
                                }
                            },
                            scaleLabel: {
                                display: false
                            }
                        }]
                    },
                    legend: {
                        labels: {
                            // This more specific font property overrides the global property
                            fontColor: '#122C4B',
                            fontFamily: "'Muli', sans-serif",
                            padding: 25,
                            boxWidth: 25,
                            fontSize: 14,
                        }
                    },
                    layout: {
                        padding: {
                            left: 10,
                            right: 10,
                            top: 0,
                            bottom: 10
                        }
                    }


                }
            });

            //Alunos por curso donnut chart
            var cty = document.getElementById('AlunosCursos').getContext('2d');
            var chart = new Chart(cty, {
                // The type of chart we want to create
                type: 'doughnut',
                // The data for our dataset
                data: {
                    labels: {!! json_encode($AlunosCursosChart->labels) !!},
                    datasets: [{
                        label: 'Alunos por curso',
                        backgroundColor: {!! json_encode($AlunosCursosChart->colours) !!},
                        borderColor: {!! json_encode($AlunosCursosChart->borderColours) !!},
                        borderWidth: 1,
                        data: {!! json_encode($AlunosCursosChart->dataset['Total']) !!}
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                callback: function(value) {
                                    if (value % 1 === 0) {
                                        return value;
                                    }
                                }
                            },
                            scaleLabel: {
                                display: false
                            }
                        }]
                    },
                    legend: {
                        labels: {
                            // This more specific font property overrides the global property
                            fontColor: '#122C4B',
                            fontFamily: "'Muli', sans-serif",
                            padding: 25,
                            boxWidth: 25,
                            fontSize: 14,
                        }
                    },
                    layout: {
                        padding: {
                            left: 10,
                            right: 10,
                            top: 0,
                            bottom: 10
                        }
                    }
                }
            });

        </script>




    </div>
    @include('footer')


</body>

</html>
