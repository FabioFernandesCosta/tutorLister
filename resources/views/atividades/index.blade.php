<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Atividades - TutorLister</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
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


    function sortTable() {

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

        <div class="AtvBtns">
            <form class="filterForm" autocomplete="off" method="GET">
                <div class="">


                    <label for="filter" class="" style="font-weight: bold">Filtro</label>
                    <input type="text" class="miniImp" id="filter" name="filter"
                        placeholder="id, usuario, requisitante, descrição..." style="height: 25px">
                </div>
                <button type="submit" class="miniBtn" style="margin-top: 0">Filtrar</button>
                <a href="{{ url('atividades') }}">
                    <button style="width: 1.5rem; margin-top:0; margin-right:25px" type="button"
                        class="miniBtn">X</button>
                </a>
            </form>
            <a href={{ url('atividades/create') }}>
                <button class="miniBtn">Novo</button>
            </a>
            <form method="POST" action="{{ route('atividade.export') }}">
                @csrf
                <input hidden value="{{ $filter }}" name="filter" type="text">


                <button class="miniBtn" type="submit">Exportar</button>

            </form>

            <button style="width: 1.5rem; margin-top:25px; margin-right:25px" type="button" class="miniBtn" onclick="showIndexCConfig()">
                <img style="margin-top: 2px" src="{{url('/image/cog.png')}}" width="16" height="16" />
            </button>
        </div>



        {{-- tabela com as informações --}}
        <div id="atvGrid"
            style="grid-template-columns: 3.5rem repeat({{ 9 }} {{-- quantidade de colunas --}} , minmax(200px, 1fr));">
            @if (count($atv) > 0)
                <div> <b> ID </b> </div>
                <div> <b> Descrição </b> </div>
                <div> <b> Usuários envolvidos </b> </div>
                <div> <b> Requisitante </b> </div>
                <div> <b> Data de realização </b> </div>
                <div> <b> Hora de realização </b> </div>
                <div> <b> Data do registro </b> </div>
                <div> <b> Hora do registro </b> </div>
                <div> <b> Carga horária da atividade </b> </div>
                <div> <b> Status </b> </div>
                @foreach ($atv as $key => $value)
                    <div onclick="location.href='{{ URL::to('atividades/' . $value->atividade_id) }}';">
                        {{ $value->atividade_id }}
                    </div>
                    <div onclick="location.href='{{ URL::to('atividades/' . $value->atividade_id) }}';" class="desc">
                        {{ $value->descricao }}
                    </div>
                    <div onclick="location.href='{{ URL::to('atividades/' . $value->atividade_id) }}';">
                        {{ $value->nome }}
                    </div>
                    <div onclick="location.href='{{ URL::to('atividades/' . $value->atividade_id) }}';">
                        {{ $value->requisitante }}
                    </div>
                    <div onclick="location.href='{{ URL::to('atividades/' . $value->atividade_id) }}';">
                        {{ $value->data_atividade }}
                    </div>
                    <div onclick="location.href='{{ URL::to('atividades/' . $value->atividade_id) }}';">
                        {{ $value->hora_atividade }}
                    </div>
                    <div onclick="location.href='{{ URL::to('atividades/' . $value->atividade_id) }}';">
                        {{ $value->data_registro }}
                    </div>
                    <div onclick="location.href='{{ URL::to('atividades/' . $value->atividade_id) }}';">
                        {{ $value->hora_registro }}
                    </div>
                    <div onclick="location.href='{{ URL::to('atividades/' . $value->atividade_id) }}';">
                        {{ $value->carga }}
                    </div>
                    <div onclick="location.href='{{ URL::to('atividades/' . $value->atividade_id) }}';">
                        {{ $value->status }}
                    </div>
                @endforeach
            @else
                <div id="indexNotFound">
                    <p>

                        Não há resistros com esse filtro
                    </p>
                </div>
            @endif
        </div>
        <div id="paginacao">
            {{ $atv->links() }}

        </div>



        <div id="indexCConfig">
            <span>colunas</span>
            <div>
                <div class="switchContainer">

                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider round"></span>
                    </label>
                    Descrição
                </div>
                <div class="switchContainer">

                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider round"></span>
                    </label>
                    Usuários envolvidos
                </div>
                <div class="switchContainer">

                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider round"></span>
                    </label>
                    Requisitante
                </div>
                <div class="switchContainer">

                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider round"></span>
                    </label>
                    Data de realização
                </div>
                <div class="switchContainer">

                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider round"></span>
                    </label>
                    Hora de realização
                </div>
                <div class="switchContainer">

                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider round"></span>
                    </label>
                    Data de registro
                </div>
                <div class="switchContainer">

                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider round"></span>
                    </label>
                    Hora de registro
                </div>
                <div class="switchContainer">

                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider round"></span>
                    </label>
                    Carga horária da atividade
                </div>
                <div class="switchContainer">

                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider round"></span>
                    </label>
                    Status
                </div>

                <button class="miniBtn" onclick="hideIndexCConfig()">OK</button>

            </div>
        </div>


</body>
<script>
    let filter = (window.location.href).split('=')[1];
    if (filter = 'undefined') {
        filter = '';
    }
    document.getElementById("filter").value = filter;
</script>

</html>
