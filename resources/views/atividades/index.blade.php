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
        {{-- botoões --}}




        <br>
        <div class="btn-right">

            <a href={{ url('atividades/create') }}>
                <button class="dt-button outline-btn">Novo</button>
            </a>
        </div>




        {{-- tabela com as informações --}}



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


            <div id="toggleBtn"></div>



            <table id="JqueryAtvTable" class="display nowrap dataTable " style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Descrição</th>
                        <th>Usuários envolvidos</th>
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


        <script defer>
            $(document).ready(function() {

                var table = $('#JqueryAtvTable').DataTable({
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
                    "ajax": '{{ url('getdata') }}',


                    "columns": [{
                            "data": "atividade_id"
                        },
                        {
                            "data": "descricao",
                            name: "descricao"
                        },
                        {
                            "data": "nome",
                            name: 'usuario.nome'
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
                    ]
                });
                $('#min, #max').on('change', function() {
                    table.draw();
                });

                

            });
        </script>
        {{-- <div id="paginacao">
                {{ $atv->links() }}
    
            </div> --}}









</body>


</html>
{{-- google auth to login --}}
