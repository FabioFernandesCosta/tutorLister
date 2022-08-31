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




            <table id="JqueryAtvImportTable" class="display nowrap dataTable " style="width:100%; cursor:pointer">
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
            <input type="file" onchange="previewFile()" accept=".csv">
            <p class="content"></p>

            <div class="column_relation">
                <span>

                    Descrição:
                </span>
                <select class="select" name="desc" id=""></select>
                <span>

                    Usuários envolvidos:
                </span>
                <select class="select" name="us" id=""></select>
                <span>
                    Requisitante:
                </span>

                <select class="select" name="req" id=""></select>
                <span>
                    Data de realização:
                </span>
                <select class="select" name="drea" id=""></select>
                <span>
                    Hora de realização:
                </span>
                <select class="select" name="hrea" id=""></select>
                <span>
                    Data do registro:
                </span>
                <select class="select" name="deg" id=""></select>
                <span>
                    Hora do registro:
                </span>
                <select class="select" name="hreg" id=""></select>
                <span>
                    Carga horária da atividade:
                </span>
                <select class="select" name="ch" id=""></select>
                <span>
                    Status:
                </span>
                <select class="select" name="st" id=""></select>
            </div>





            <script defer>
                var table = $('#JqueryAtvImportTable').DataTable({

                    "columns": [{
                            data: 'ID',
                            name: 'id'
                        },
                        {
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
                            data: 'data_registro',
                            name: 'data_registro'
                        },
                        {
                            data: 'hora_registro',
                            name: 'hora_registro'
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

                    reader.addEventListener("load", () => {
                        // this will then display the file
                        fileCsv = $.csv.toObjects(reader.result);

                        var select = $('.select');
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

                        console.log();
                        //table.rows.add(fileCsv).draw();
                    }, false);

                    if (file) {
                        reader.readAsText(file);
                    }
                }

                $("#populateTable").click(function() {
                    console.log((typeof(($("#csvImport").val()))));
                    table.rows.add($.csv.toObjects($("#csvImport").val())).draw();
                });
            </script>
        </div>
    </div>


</body>

</html>
