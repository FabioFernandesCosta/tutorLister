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
    </style>


</head>

{{-- clicar-carregar --}}
{{-- https://htmldom.dev/sort-a-table-by-clicking-its-headers/ - sort form --}}
<script>
    function getClicked(id) {
        console.log(id)
    }




    function switchShowAtvBtns() {
        var btns = document.getElementById("AtvBtns");
        if (btns.style.display === "none") {
            btns.style.display = "";
        } else {
            btns.style.display = "none";
        }
    }

    function hideIndexCConfig() {
        document.getElementById("indexCConfig").style.display = "none";
    }

    function showIndexCConfig() {
        document.getElementById("indexCConfig").style.display = "block";
    }
</script>



@include('sidemenu')

<body class="antialiased" id="eventBody">
    <div class="wrapAll">
        <span id="pageTitle">
            Atividades
        </span>
        {{-- botoões --}}

        <div id="colapsibleContainer">
            <div class="switchAtvBtnsContainer">
                <button type="button" class="switchAtvBtns miniBtn" onclick="switchShowAtvBtns()">Menu ▼</button>
            </div>


            <div class="AtvBtns" id="AtvBtns">
                <form class="filterForm" autocomplete="off" method="GET">
                    <div class="">

                        <div id="indexCConfig">
                            <span>colunas</span>
                            <div>
                                <div class="switchContainer">

                                    <label class="switch">

                                        <input type="checkbox"
                                            name="colunas[descricao]"@if (isset($colunas['descricao'])) checked @endif>

                                        <span class="slider round"></span>
                                    </label>
                                    Descrição
                                </div>
                                <div class="switchContainer">

                                    <label class="switch">

                                        <input type="checkbox"
                                            name="colunas[usuario]"@if (isset($colunas['usuario'])) checked @endif>
                                        <span class="slider round"></span>
                                    </label>
                                    Usuários envolvidos
                                </div>
                                <div class="switchContainer">

                                    <label class="switch">
                                        <input type="checkbox"
                                            name="colunas[requisitante]"@if (isset($colunas['requisitante'])) checked @endif>

                                        <span class="slider round"></span>
                                    </label>
                                    Requisitante
                                </div>
                                <div class="switchContainer">

                                    <label class="switch">
                                        <input type="checkbox"
                                            name="colunas[drealizacao]"@if (isset($colunas['drealizacao'])) checked @endif>



                                        <span class="slider round"></span>
                                    </label>
                                    Data de realização
                                </div>
                                <div class="switchContainer">

                                    <label class="switch">
                                        <input type="checkbox"
                                            name="colunas[hrealizacao]"@if (isset($colunas['hrealizacao'])) checked @endif>

                                        <span class="slider round"></span>
                                    </label>
                                    Hora de realização
                                </div>
                                <div class="switchContainer">

                                    <label class="switch">
                                        <input type="checkbox"
                                            name="colunas[dregistro]"@if (isset($colunas['dregistro'])) checked @endif>

                                        <span class="slider round"></span>
                                    </label>
                                    Data de registro
                                </div>
                                <div class="switchContainer">

                                    <label class="switch">
                                        <input type="checkbox"
                                            name="colunas[hregistro]"@if (isset($colunas['hregistro'])) checked @endif>

                                        <span class="slider round"></span>
                                    </label>
                                    Hora de registro
                                </div>
                                <div class="switchContainer">

                                    <label class="switch">
                                        <input type="checkbox"
                                            name="colunas[ch]"@if (isset($colunas['ch'])) checked @endif>

                                        <span class="slider round"></span>
                                    </label>
                                    Carga horária da atividade
                                </div>
                                <div class="switchContainer">

                                    <label class="switch">
                                        <input type="checkbox"
                                            name="colunas[status]"@if (isset($colunas['status'])) checked @endif>

                                        <span class="slider round"></span>
                                    </label>
                                    Status
                                </div>

                                <button class="miniBtn" onclick="hideIndexCConfig()" type="submit">OK</button>

                            </div>
                        </div>
                        <label for="filter" class="HideOnColapse" style="font-weight: bold">Filtro</label>
                        <input type="text" class="miniImp" id="filter" name="filter"
                            placeholder="id, usuario, requisitante, descrição..." style="height: 25px">
                    </div>
                    <button type="submit" class="miniBtn" style="margin-top: 0">Filtrar</button>
                    <a href="{{ url('atividades') }}">
                        <button id="xbutton" style="width: 1.5rem; margin-top:0; margin-right:25px" type="button"
                            class="miniBtn">X</button>
                    </a>
                </form>
                <div style="display: flex; gap:5px">

                    <a href={{ url('atividades/create') }}>
                        <button class="miniBtn">Novo</button>
                    </a>
                    <form method="POST" action="{{ route('atividade.export') }}">
                        @csrf
                        <input hidden value="{{ $filter }}" name="filter" type="text">


                        <button class="miniBtn" type="submit">Exportar</button>

                    </form>

                    <button style="width: 1.5rem; margin-top:25px; margin-right:25px" type="button" class="miniBtn"
                        onclick="showIndexCConfig()">
                        <img style="margin-top: 2px" src="{{ url('/image/cog.png') }}" width="16"
                            height="16" />
                    </button>
                </div>
            </div>

        </div>


        {{-- tabela com as informações --}}



        <div class="tableContainer">
            {{-- jquery datatables table --}}

            <link rel="stylesheet" type="text/css"
                href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.css">
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
            <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js">
            </script>
            <script type="text/javascript" charset="utf8"
                src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
            <script type="text/javascript" charset="utf8"
                src="https://cdn.datatables.net/datetime/1.1.2/js/dataTables.dateTime.min.js"></script>

                
            



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
                        'copy', 'csv', 'excel', 'pdf', 'print'
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
<script>
    let filter = (window.location.href).split('=')[1];
    if (filter = 'undefined') {
        filter = '';
    }
    document.getElementById("filter").value = filter;

    switchShowAtvBtns();
</script>

</html>
{{-- google auth to login --}}
