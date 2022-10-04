<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Ponto - TutorLister</title>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <!-- CSS only -->
    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }

        /* outline button */
    </style>


</head>

{{-- clicar-carregar --}}
{{-- https://htmldom.dev/sort-a-table-by-clicking-its-headers/ - sort form --}}




@include('sidemenu')

<body class="antialiased" id="eventBody">
    <div class="wrapAll">
        <span id="pageTitle">
            Pontos
        </span>


        <div class="tableContainer">
            {{-- jquery datatables table --}}

            <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.css">
            <link rel="stylesheet" type="text/css"
                href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
            <link rel="stylesheet" type="text/css"
                href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.dataTables.css">
            <link rel="stylesheet" type="text/css"
                href="https://cdn.datatables.net/datetime/1.1.2/css/dataTables.dateTime.min.css">

            <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-1.12.4.js"></script>
            <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.js"></script>
            <script type="text/javascript" charset="utf8"
                src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
            <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js">
            </script>
            <script type="text/javascript" charset="utf8"
                src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
            <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js">
            </script>
            <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js">
            </script>

            <script type="text/javascript" charset="utf8"
                src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
            <script type="text/javascript" charset="utf8"
                src="https://cdn.datatables.net/datetime/1.1.2/js/dataTables.dateTime.min.js"></script>
            <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.colVis.min.js">
            </script>
            <script type="text/javascript" charset="utf8"
                src="//cdn.datatables.net/plug-ins/1.12.1/filtering/row-based/range_dates.js"></script>


            <div style="width: 35rem">
                <h3 style="margin-top: 3.5rem">Registrar ponto</h3>
                <div class="atvDetalhes" style="margin-top: 1rem">
                    {{ Form::open(['url' => 'ponto', 'autocomplete' => 'off', 'action' => 'sistemaPontoController@store']) }}
                    <div id="repart2">

                        <p>Dia:
                            <input type="date" id="dia" readonly class="pontoTimes" name="dia">
                        </p>
                        <p>Hora:
                            <input type="time" id="hora" readonly class="pontoTimes" name="hora">
                            {{-- <input type="hidden"> --}}
                        </p>
                        {{-- close --}}
                    </div>
                    {{-- <div class="closeBtn" style="margin-top: 1rem">
                        <button type="submit" class="dt-button" style="margin-top: 1rem">Registrar</button>
                    </div> --}}
                    {{ Form::submit('Registrar', ['class' => ' mt-3 dt-button', 'style' => 'margin-top: 45px']) }}
                    {{ Form::close() }}
                    {{-- <button class="dt-button">test</button> --}}
                </div>
                <h3 style="margin-top: 3.5rem">Seus registros</h3>
                <div id="indexButtons" style="display: grid; grid-template-columns: 50% 50%; margin-top: 1rem">
                </div>
                <table id="JqueryAtvTable" class="display nowrap dataTable " style="width:100%; cursor:pointer">
                    <thead>
                        <tr>
                            {{-- list of returned columns = ['requisitante_id', 'nome', 'email', 'telefone', 'empresa'] --}}
                            <th>Dia</th>
                            <th>Entrada</th>
                            <th>Saída</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @include('footer')

    <script defer>
        $(document).ready(function() {

            //appent at <input id="dia"> value the current day in format dd/mm/yyyy
            var dia = moment().format('YYYY-MM-DD');
            $('#dia').val(dia);

            //constantly append the current time in format hh:mm:ss into <p id="hora">
            setInterval(function() {
                var hora = moment().format('HH:mm:ss');
                $('#hora').val(hora);
            }, 1000);


            var table = $('#JqueryAtvTable').DataTable({
                //order by the first column and second column in descending order and null as first
                order: [
                    [0, 'desc'],
                    [1, 'desc'],
                    [2, 'desc'],
                ],
                

                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "todos"]
                ],
                scrollX: true,

                dom: 'Brtlip',

                buttons: [
                    'csv', 'excel', 'pdf', 'print' //'columnsToggle'
                ],


                "createdRow": function(row, data, dataIndex) {
                    $(row).attr('onclick', 'location.href="{{ URL::to('requisitantes') }}/' + data
                        .requisitante_id + '";');
                },

                "processing": true,
                "serverSide": true,
                ajax: {
                    url: "{{ url('ponto/getdata') }}",
                },

                //linguagem
                "language": {
                    "lengthMenu": "Mostrar _MENU_ registros por página",
                    "zeroRecords": "Nada encontrado - desculpe",
                    "info": "Mostrando página _PAGE_ de _PAGES_",
                    "infoEmpty": "Nenhum registro disponível",
                    "infoFiltered": "(filtrado de _MAX_ registros no total)",
                    "search": "Pesquisar:",
                    "paginate": {
                        "first": "Primeiro",
                        "last": "Último",
                        "next": "Próximo",
                        "previous": "Anterior"
                    },
                    //colvis button language
                    buttons: {
                        colvis: 'Mostrar/Ocultar colunas'
                    }
                },

                "columns": [{
                        "data": "dia"
                    },
                    {
                        "data": "hora_inicio",
                    },
                    {
                        "data": "hora_fim"
                    }
                ],
            });


            //detach buttons class dtbuttons from table and put as first child under indexButtons div
            var indexButtons = $('#indexButtons');
            var buttons = $('.dt-buttons').detach();
            indexButtons.prepend(buttons);


        });
    </script>

</body>

</html>
