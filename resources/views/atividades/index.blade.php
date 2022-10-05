<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Atividades - TutorLister</title>

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
            Atividades
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


            <table border="0" cellspacing="5" cellpadding="5" id="dateFilter">
                <tbody>
                    <tr>
                        <td>Data minima:</td>
                        <td><input style="border: 1px solid #aaa; border-radius: 3px" type="date" id="min"
                                name="min"></td>
                        <td>Data maxima:</td>
                        <td><input style="border: 1px solid #aaa; border-radius: 3px" type="date" id="max"
                                name="max"></td>
                    </tr>
                    <tr>
                    </tr>
                </tbody>
            </table>
            <div id="indexButtons" style="display: grid; grid-template-columns: 50% 50%; margin-top: 3.5rem">

                <div class="btn-right">

                    <a href="{{ url('atividades/import') }}" style="text-decoration: none">
                        <button class="dt-button">Importar</button>
                        <a href={{ url('atividades/create') }}>
                            <button class="dt-button">Novo</button>
                        </a>
                </div>
            </div>
            <table id="JqueryAtvTable" class="display nowrap dataTable " style="width:100%; cursor:pointer">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Descrição</th>
                        <th>Alunos envolvidos</th>
                        <th>Requisitante</th>
                        <th>Data de realização</th>
                        <th>Hora de realização</th>
                        <th>Data do registro</th>
                        <th>Hora do registro</th>
                        <th>Carga horária da atividade</th>
                        <th>Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    @include('footer')


        <script defer>
            var minDate, maxDate;

            minDateFilter = "";
            maxDateFilter = "";

            $(document).ready(function() {

                var table = $('#JqueryAtvTable').DataTable({
                    order: [[0, 'desc']],
                    columnDefs: [{
                        orderable: false,
                        targets: [2, 3]
                    }],

                    //max characters per column

                    "lengthMenu": [
                        [10, 25, 50, -1],
                        [10, 25, 50, "All"]
                    ],
                    scrollX: true,

                    dom: 'Bfrtlip',

                    buttons: [
                        'colvis', 'csv', 'excel', 'pdf', 'print' //'columnsToggle'
                    ],


                    "createdRow": function(row, data, dataIndex) {
                        $(row).attr('onclick', 'location.href="{{ URL::to('atividades') }}/' + data
                            .atividade_id + '";');
                    },

                    "processing": true,
                    "serverSide": true,
                    ajax: {
                        url: "{{ url('atividades/getdata') }}",
                        data: function(d) {
                            d.min = minDateFilter;
                            d.max = maxDateFilter;
                        }
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
                            "data": "atividade_id"
                        },
                        {
                            "data": "descricao",
                            name: "descricao"
                        },
                        {
                            "data": "nomeUs",
                            name: "usuario.nome"
                        },
                        {
                            "data": "requisitante",
                            name: 'requisitante.nome'
                        },
                        {
                            "data": "data_atividade"
                        },
                        {
                            "data": "hora_atividade"
                        },
                        {
                            "data": "data_registro"
                        },
                        {
                            "data": "hora_registro"
                        },
                        {
                            "data": "carga"
                        },
                        {
                            "data": "status"
                        }
                    ],
                });


                //detach buttons class dtbuttons from table and put as first child under indexButtons div
                var indexButtons = $('#indexButtons');
                var buttons = $('.dt-buttons').detach();
                indexButtons.prepend(buttons);


                //detach dateFilter table and put it side to side with JqueryAtvTable_filter
                var dateFilter = $('#dateFilter').detach();
                $('#JqueryAtvTable_filter').after(dateFilter);



                //once #min or #max are changed, filter the table date columns with #min and #max values
                $('#min').change(function() {
                    minDateFilter = $("#min").val();
                    console.log("test");
                    table.draw();
                });
                $('#max').change(function() {
                    maxDateFilter = $("#max").val();
                    table.draw();
                });

            });
        </script>
        
</body>
</html>
