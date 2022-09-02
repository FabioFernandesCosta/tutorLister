<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Importar - TutorLister</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
    </style>
</head>

{{-- clicar-carregar --}}



@include('sidemenu')

<body class="antialiased" id="eventBody">
    </div>
    <div class="wrapAll">
        <span id="pageTitle">
            Importar
        </span>

        <div class="tableContainer">

            <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.css">
            <link rel="stylesheet" type="text/css"
                href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
            <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-1.12.4.js"></script>
            <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.js"></script>
            <script type="text/javascript" charset="utf8"
                src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>

            <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-csv/1.0.21/jquery.csv.min.js"
                charset="utf8"></script>




            {{ Form::open(['url' => 'atividades/import/store', 'class' => 'atvForm', 'autocomplete' => 'off', 'action' => 'atividadeController@import_atv']) }}
            <table id="JqueryAtvImportTable" class="display nowrap dataTable " style="width:100%; cursor:pointer">
                <thead>
                    <tr>
                        <th>Descrição</th>
                        <th>Usuários envolvidos</th>
                        <th>Requisitante</th>
                        <th>Data de realização</th>
                        <th>Hora de realização</th>
                        <th>Carga horária da atividade</th>
                        <th>Status</th>
                    </tr>
                </thead>
            </table>
            {{-- form submit --}}




            <input type="file" onchange="previewFile()" accept=".csv">
            <p class="content"></p>

            <div class="column_relation">
                <span>

                    Descrição:
                </span>
                <select class="select" name="descricao" id=""></select>
                <span>

                    Usuários envolvidos:
                </span>
                <select class="select" name="usuarios_envolvidos" id=""></select>
                <span>
                    Requisitante:
                </span>

                <select class="select" name="requisitante"></select>
                <span>
                    Data de realização:
                </span>
                <select class="select" name="data_realizacao"></select>
                <span>
                    Hora de realização:
                </span>
                <select class="select" name="hora_realizacao"></select>
                <span>
                    Carga horária da atividade:
                </span>
                <select class="select" name="carga_horaria"></select>
                <span>
                    Status:
                </span>
                <select class="select" name="status"></select>

            </div>



            <div class="">
                <button id="mapButton" type="button" class="dt-button outline-btn">Carregar na tabela</button>
                {{ Form::submit('Registrar', ['class' => ' mt-3 dt-button', 'style' => 'margin-top: 45px', 'onclick' => "return confirm('Confirmar?');"]) }}
            </div>


            {{ Form::close() }}





            <script defer>
                var table = $('#JqueryAtvImportTable').DataTable({

                    //render all cells with inputs
                    "columnDefs": [{
                        "targets": "_all",
                        "createdCell": function(td, cellData, rowData, row, col) {
                            $(td).html('<input type="text" name=" ' + col + '[]" value="' + cellData + '" />');
                        }
                    }],
                    scrollX: true,
                    paging: false,

                    "columns": [{
                            data: 'descricao',
                            name: 'descricao'
                        },
                        {
                            data: 'usuarios_envolvidos',
                            name: 'usuarios_envolvidos'
                        },
                        {
                            data: 'requisitante',
                            name: 'requisitante'
                        },
                        {
                            data: 'data_realizacao',
                            name: 'data_realizacao'
                        },
                        {
                            data: 'hora_realizacao',
                            name: 'hora_realizacao'
                        },
                        {
                            data: 'carga_horaria',
                            name: 'carga_horaria'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        }
                    ],
                });

                function previewFile() {
                    const content = document.querySelector('.content');
                    const [file] = document.querySelector('input[type=file]').files;
                    const reader = new FileReader();
                    var fileCsv;
                    var select = $('.select');

                    reader.addEventListener("load", () => {
                        // this will then display the file
                        fileCsv = $.csv.toObjects(reader.result);

                        select
                            .append($("<option></option>")
                                .attr("value", "")
                                .text(""));


                        //select options
                        $.each(fileCsv[0], function(key, value) {
                            select
                                .append($("<option></option>")
                                    .attr("value", key)
                                    .text(key));

                        });

                        //map datatable based on selects values when button is clicked
                        $('#mapButton').click(function() {
                            var descricao = $('select[name="descricao"]').val();
                            var usuarios_envolvidos = $('select[name="usuarios_envolvidos"]').val();
                            var requisitante = $('select[name="requisitante"]').val();
                            var data_realizacao = $('select[name="data_realizacao"]').val();
                            var hora_realizacao = $('select[name="hora_realizacao"]').val();
                            var carga_horaria = $('select[name="carga_horaria"]').val();
                            var status = $('select[name="status"]').val();

                            var data = fileCsv.map(function(item) {
                                return {
                                    descricao: item[descricao],
                                    usuarios_envolvidos: item[usuarios_envolvidos],
                                    requisitante: item[requisitante],
                                    data_realizacao: item[data_realizacao],
                                    hora_realizacao: item[hora_realizacao],
                                    carga_horaria: item[carga_horaria],
                                    status: item[status]
                                }
                            });
                            //if any item in the array is null or undefined, set it to am empty string
                            data = data.map(function(item) {
                                for (var key in item) {
                                    if (item[key] == null) {
                                        item[key] = "";
                                    }
                                }
                                return item;
                            });

                            table.clear().draw();
                            table.rows.add(data).draw();
                        });


                        //table.rows.add(fileCsv).draw();
                    }, false);

                    if (file) {
                        reader.readAsText(file);
                    }
                }
            </script>
        </div>
    </div>


</body>

</html>
