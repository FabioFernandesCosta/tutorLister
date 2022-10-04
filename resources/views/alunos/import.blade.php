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
            Importar alunos
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




            {{ Form::open(['url' => 'alunos/import/store', 'class' => 'atvForm', 'autocomplete' => 'off', 'action' => 'alunosController@import_aluno']) }}

            <input type="file" class="fileInput" onchange="previewFile()" accept=".csv">



            <div class="column_relation">
                <span>

                    Nome
                </span>
                <select class="select" name="nome" id=""></select>
                <span>

                    Curso
                </span>
                <select class="select" name="curso"></select>
                <span>
                    Horário
                </span>
                <select class="select" name="horario"></select>
                <span>
                    E-mail
                </span>
                <select class="select" name="email" id=""></select>


                <span>
                    Telefone
                </span>
                <select class="select" name="telefone"></select>
                

            </div>
            <button id="mapButton" type="button" class="dt-button outline-btn" style="margin-top: 10px">Carregar na tabela</button>





            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li class="ulError">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <table id="JqueryAtvImportTable" class="display nowrap dataTable " style="width:100%; cursor:pointer">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Curso</th>
                        <th>Horário</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        
                    </tr>
                </thead>
            </table>
            {{-- form submit --}}

            <div class="atvFormBtn">

                <a style="margin: auto; margin-left: 0" href={{ url('atividades/') }}>
                    <button class="dt-button">Cancelar</button>
                </a>
                <div class="btn-right">
                    {{ Form::submit('Salvar', ['class' => ' mt-3 dt-button', 'onclick' => "return confirm('Confirmar?');"]) }}
                </div>
            </div>
            



            {{ Form::close() }}



            @php
                //dd(old('0'));
                $data = [
                    '0' => old('0'),
                    '1' => old('1'),
                    '2' => old('2'),
                    '3' => old('3'),
                    '4' => old('4'),
                ];
                if ($data[0] != null) {
                    $data = array_map(null, $data[0], $data[1], $data[2], $data[3], $data[4]);
                    //dd($data);
                }
                
            @endphp

            <script defer>
                if ("{{ count($data) !== 0 }}") {
                    var data = (@json($data));
                    //console.log(data);
                    if (data[0] != null) {

                        var data = data.map(function(item) {
                            return {
                                nome: item[0],
                                curso: item[1],
                                horario: item[2],
                                email: item[3],
                                telefone: item[4]
                            }
                        });
                    } else {
                        var data = [];
                    }

                }
                var table = $('#JqueryAtvImportTable').DataTable({

                    //if data[0] is not null, then it will be used to fill the table
                    "data": data,


                    "columnDefs": [{
                        "targets": "_all",
                        "createdCell": function(td, cellData, rowData, row, col) {

                            if (data[0] == null) {
                                $(td).html('<input type="text" name="' + col + '[]" value="' + cellData +
                                    '" />');
                            }
                        }
                    }],
                    scrollX: true,
                    paging: false,
                    //datatable linguagem
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
                    },

                    "columns": [{
                            data: 'nome',
                            name: 'descricao'
                        },
                        {
                            data: 'curso',
                            name: 'usuarios_envolvidos'
                        },
                        {
                            data: 'horario',
                            name: 'requisitante'
                        },
                        {
                            data: 'email',
                            name: 'data_realizacao'
                        },
                        {
                            data: 'telefone',
                            name: 'hora_realizacao'
                        }
                    ],
                });

                function load_old() {
                    if (data) {
                        //console.log(data);
                        table.clear().draw();
                        table.rows.add(data).draw();
                    }
                }

                function previewFile() {
                    const content = document.querySelector('.content');
                    const [file] = document.querySelector('input[type=file]').files;
                    const reader = new FileReader();
                    var fileCsv;
                    var select = $('.select');

                    reader.addEventListener("load", () => {
                        //remove all options from select
                        

                        select.empty();
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
                            var nome = $('select[name="nome"]').val();
                            var curso = $('select[name="curso"]').val();
                            var horario = $('select[name="horario"]').val();
                            var email = $('select[name="email"]').val();
                            var telefone = $('select[name="telefone"]').val();
                            

                            var data = fileCsv.map(function(item) {
                                return {
                                    nome: item[nome],
                                    curso: item[curso],
                                    horario: item[horario],
                                    email: item[email],
                                    telefone: item[telefone]

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
                if (data[0] != null) {

                    load_old();
                }
            </script>
        </div>
    </div>
    @include('footer')
</body>

</html>