<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Aluno ID-{{ $aluno->usuario_id }} - TutorLister</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
    </style>
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
</head>

{{-- clicar-carregar --}}



@include('sidemenu')

<body class="antialiased" id="eventBody">

    </div>
    <div class="wrapAll">

        <h1>Aluno: {{ $aluno->nome }}</h1>
        <h3>ID-{{ $aluno->usuario_id }}</h3>


        {{-- detalhes do selecionado --}}
        <div class="AtvBtns">

            <a style="text-decoration: none;" href={{ url('alunos') }}>
                <button class="dt-button">Voltar</button>
            </a>
            {{-- @php
                dd($aluno->usuario_id);
            @endphp --}}
            <a style="text-decoration: none;" href={{ url('alunos/' . $aluno->usuario_id . '/edit') }}>
                <button class="dt-button">Editar</button>
            </a>

        </div>



        <div class="atvDetalhes" style="margin-top: 0">

            <form class="p-3 mt-3 atvForm">


                <div>
                    {{-- botões --}}
                    {{-- Inputs --}}
                    {{-- {{ Html::ul($errors->get('InvolvedUsers'), ['class' => 'ulError']) }} --}}
                    <div id="repart2">
                        <div>
                            <h3 class="itemTittle">Detalhes</h3>
                            <span>Nome</span>
                            {{-- {{ Html::ul($errors->get('Requisitante'), ['class' => 'ulError']) }} --}}
                            <div class="form-field form-field-little" id="req"> <span></span>
                                <input readonly type="text" value="{{ $aluno->nome }}" name="nome"
                                    id="usuario">
                            </div>
                            <div>
                                <span>Tem acesso ao sistema?</span>
                                <div class="form-field form-field-littlePlus" id="ac"> <span></span>

                                    {{-- input version of the select above --}}
                                    <input readonly type="text" value="{{ $aluno->nivel_de_acesso }}" name="acesso"
                                        id="acesso">


                                </div>
                            </div>
                            <div>
                                <span>Está ativo?</span>
                                <div class="form-field form-field-littlePlus" id="at"> <span></span>

                                    {{-- input version of the select above --}}
                                    <input readonly type="text" value="{{ $aluno->ativo }}" name="ativo"
                                        id="ativo">
                                </div>
                            </div>
                            <div>
                                <span>Concluiu treinamento?</span>
                                <div class="form-field form-field-littlePlus" id="tr"> <span></span>

                                    {{-- input version of the select above --}}
                                    <input readonly type="text" value="{{ $aluno->treinamento_concluido }}"
                                        name="treinamento_concluido" id="treinamento_concluido">
                                </div>
                            </div>

                            <span>Curso</span>
                            {{-- {{ Html::ul($errors->get('Requisitante'), ['class' => 'ulError']) }} --}}
                            <div class="form-field form-field-little" id="req"> <span></span>
                                <input readonly type="text" value="{{ $aluno->curso }}" name="curso"
                                    id="curso">
                            </div>

                            <span>Horário do curso</span>
                            {{-- {{ Html::ul($errors->get('Requisitante'), ['class' => 'ulError']) }} --}}
                            <div class="form-field form-field-little" id="req"> <span></span>
                                <input readonly type="text" value="{{ $aluno->horario }}" name="horario"
                                    id="horario">
                            </div>

                            <span>E-mail</span>
                            <div class="form-field form-field-little" id="req"> <span></span>
                                <input readonly type="email" value="{{ $aluno->email }}" name="email"
                                    id="email">
                            </div>
                            <span>Telefone</span>
                            <div class="form-field form-field-little" id="req"> <span></span>
                                <input readonly type="tel" value="{{ $aluno->telefone }}" name="telefone"
                                    id="telefone" pattern="[0-9]{2} [0-9]{9}" placeholder="43 123456789">
                            </div>


                        </div>
                        <div id="userContainer">
                            <h3 class="itemTittle">Últimas atividade de {{ $aluno->nome }}</h3>

                            {{-- table using $atividades (descricao, data_atividade and hora_atividade) as its fields and atividade_id as link to atividades --}}
                            <table id="av15" class="display">
                                <thead>
                                    <tr>
                                        <th>Descrição</th>
                                        <th>Data</th>
                                        <th>Hora</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>

                        </div>

                        <script>
                            //datatable av15 using route alunos/{id}/atividades
                            $(document).ready(function() {
                                $('#av15').DataTable({
                                    "ordering": false,
                                    searching: false,
                                    paging: false,
                                    info: false,
                                    responsive: true,
                                    scrollX: true,

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

                                    "createdRow": function(row, data, dataIndex) {
                                        $(row).attr('onclick', 'location.href="{{ URL::to('atividades') }}/' + data
                                            .atividade_id + '";');
                                    },
                                    processing: true,
                                    serverSide: true,
                                    ajax: '{{ url('alunos/' . $aluno->usuario_id . '/atividades') }}',
                                    columns: [{
                                            data: 'descricao',
                                            name: 'descricao'
                                        },
                                        {
                                            data: 'data_atividade',
                                            name: 'data_atividade'
                                        },
                                        {
                                            data: 'hora_atividade',
                                            name: 'hora_atividade'
                                        },
                                    ]
                                });

                            });
                        </script>


                    </div>

                </div>



            </form>
        </div>
        @include('history')
    </div>
    @include('footer')

</body>

</html>
