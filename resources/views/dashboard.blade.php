<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Dashboard - TutorLister</title>
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
        <span id="pageTitle">
            {{-- if theres no $dashboardType echo Dashboard NPI, else echo Dashboard $dashboardType --}}

            Dashboard {{ (!isset($dashboardType) or ($dashboardType == 'npi')) ? 'NPI' : 'Aluno Tutor' }}

        </span>
        {{-- button to dashboard.post route that allow auth user that has npi = 1 and aluno_tutor = 1 to switch between npi dashboard and aluno tutor dashboard, it has a form that send org, org = 1 if $dashboardType not set or = 2, else = 1 --}}
        @if (Auth::user()->npi == 1 && Auth::user()->aluno_tutor == 1)
            <form action="{{ route('dashboard.post') }}" method="POST">
                @csrf
                <input type="hidden" name="org"
                    value="{{ (!isset($dashboardType) or $dashboardType == 'npi') ? 'aluno_tutor' : 'npi' }}">
                <button type="submit" class="dt-button btn-primary">Trocar para Dashboard
                    {{ (!isset($dashboardType) or $dashboardType == 'npi') ? 'Aluno Tutor' : 'NPI' }}</button>
            </form>
        @endif


        <div id="repart2Canvas">
            <div class="chartContainer">
                <canvas id="AtvChart"></canvas>
            </div>
            <div class="chartContainer">
                <canvas id="AlunosCursos"></canvas>
            </div>
            <div class="chartContainer">
                <canvas id="AlunosAtividadesTop10"></canvas>
            </div>
            <div class="chartContainer">
                <canvas id="alunosTreinamento"></canvas>
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
                            backgroundColor: {!! json_encode($chart->colours[0]) !!},
                            borderColor: {!! json_encode($chart->borderColours[0]) !!},
                            borderWidth: 1,
                            data: {!! json_encode($chart->dataset['Total']) !!}
                        },
                        {
                            label: 'atividades abertas',
                            backgroundColor: {!! json_encode($chart->colours[1]) !!},
                            borderColor: {!! json_encode($chart->borderColours[1]) !!},
                            borderWidth: 1,
                            data: {!! json_encode($chart->dataset['Abertas']) !!},
                            hidden: true,
                        },
                        {
                            label: 'atividades fechadas',
                            backgroundColor: {!! json_encode($chart->colours[2]) !!},
                            borderColor: {!! json_encode($chart->borderColours[2]) !!},
                            borderWidth: 1,
                            data: {!! json_encode($chart->dataset['Fechadas']) !!},
                            hidden: true,
                        },
                        {
                            label: 'atividades em andamento',
                            backgroundColor: {!! json_encode($chart->colours[3]) !!},
                            borderColor: {!! json_encode($chart->borderColours[3]) !!},
                            borderWidth: 1,
                            data: {!! json_encode($chart->dataset['Em andamento']) !!},
                            hidden: true,
                        },
                        {
                            label: 'atividades canceladas',
                            backgroundColor: {!! json_encode($chart->colours[4]) !!},
                            borderColor: {!! json_encode($chart->borderColours[4]) !!},
                            borderWidth: 1,
                            data: {!! json_encode($chart->dataset['Canceladas']) !!},
                            hidden: true,
                        }


                    ]
                },
                // Configuration options go here
                options: {
                    responsive: true,
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
                    },
                    //title
                    title: {
                        display: true,
                        text: 'Atividades nos últimos meses',
                        fontSize: 16,
                        fontColor: '#122C4B',
                        fontFamily: "'Muli', sans-serif",
                        padding: 15,
                    },
                    //set chart background color to ##e9e9e9


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
                        label: 'Alunos ativos por curso',
                        backgroundColor: {!! json_encode($AlunosCursosChart->colours) !!},
                        borderColor: {!! json_encode($AlunosCursosChart->borderColours) !!},
                        borderWidth: 1,
                        data: {!! json_encode($AlunosCursosChart->dataset['Total']) !!}
                    }]
                },
                options: {
                    responsive: true,
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
                    },
                    //title
                    title: {
                        display: true,
                        text: 'Alunos ativos por curso',
                        fontSize: 16,
                        fontColor: '#122C4B',
                        fontFamily: "'Muli', sans-serif",
                        padding: 15,
                    },
                }
            });

            //10 Alunos with most atividades side bar chart
            ctz = document.getElementById('AlunosAtividadesTop10').getContext('2d');
            var chart = new Chart(ctz, {
                // The type of chart we want to create
                type: 'bar',
                // The data for our dataset
                data: {
                    labels: {!! json_encode($AlunosAtividadesChart->labels) !!},
                    datasets: [{
                        axis: 'x',
                        //label: 'Top 10 alunos com mais atividades',
                        backgroundColor: {!! json_encode($AlunosAtividadesChart->colours) !!},
                        borderColor: {!! json_encode($AlunosAtividadesChart->borderColours) !!},
                        borderWidth: 1,
                        data: {!! json_encode($AlunosAtividadesChart->dataset['Total']) !!}
                    }]
                },
                options: {
                    responsive: true,
                    //make it a side bar chart
                    indexAxis: 'x',
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
                        display: false
                    },
                    layout: {
                        padding: {
                            left: 10,
                            right: 10,
                            top: 0,
                            bottom: 10
                        }
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.yLabel;
                            }
                        }
                    },
                    //title
                    title: {
                        display: true,
                        text: 'Top 10 alunos com mais atividades',
                        fontSize: 16,
                        fontColor: '#122C4B',
                        fontFamily: "'Muli', sans-serif",
                        padding: 15,
                    },


                }
            });

            cta = document.getElementById('alunosTreinamento').getContext('2d'); //$AlunosTreinoConcluidoChart
            //doughnut chart
            var chart = new Chart(cta, {
                // The type of chart we want to create
                type: 'doughnut',
                // The data for our dataset
                data: {
                    labels: {!! json_encode($AlunosTreinoConcluidoChart->labels) !!},
                    datasets: [{
                        label: 'Alunos ativos por curso',
                        backgroundColor: {!! json_encode($AlunosTreinoConcluidoChart->colours) !!},
                        borderColor: {!! json_encode($AlunosTreinoConcluidoChart->borderColours) !!},
                        borderWidth: 1,
                        data: {!! json_encode($AlunosTreinoConcluidoChart->dataset['Total']) !!}
                    }]
                },
                options: {
                    responsive: true,
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
                    },
                    //title
                    title: {
                        display: true,
                        text: 'Alunos com treinamento concluído',
                        fontSize: 16,
                        fontColor: '#122C4B',
                        fontFamily: "'Muli', sans-serif",
                        padding: 15,
                    },
                }
            });
        </script>




    </div>
    @include('footer')


</body>

</html>
