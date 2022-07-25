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

                    <div id="indexCConfig">
                        <span>colunas</span>
                        <div>
                            <div class="switchContainer">

                                <label class="switch">
                                    @if(isset($colunas['descricao']))
                                    <input type="checkbox" name="colunas[descricao]" checked>
                                    @else
                                    <input type="checkbox" name="colunas[descricao]">
                                    @endif
                                    <span class="slider round"></span>
                                </label>
                                Descrição
                            </div>
                            <div class="switchContainer">

                                <label class="switch">
                                    @if(isset($colunas['usuario']))
                                    <input type="checkbox" name="colunas[usuario]" checked>
                                    @else
                                    <input type="checkbox" name="colunas[usuario]">
                                    @endif
                                    <span class="slider round"></span>
                                </label>
                                Usuários envolvidos
                            </div>
                            <div class="switchContainer">

                                <label class="switch">
                                    @if(isset($colunas['requisitante']))
                                    <input type="checkbox" name="colunas[requisitante]" checked>
                                    @else
                                    <input type="checkbox" name="colunas[requisitante]">
                                    @endif
                                    <span class="slider round"></span>
                                </label>
                                Requisitante
                            </div>
                            <div class="switchContainer">

                                <label class="switch">
                                    @if(isset($colunas['drealizacao']))
                                    <input type="checkbox" name="colunas[drealizacao]" checked>
                                    @else
                                    <input type="checkbox" name="colunas[drealizacao]">
                                    @endif
                                    <span class="slider round"></span>
                                </label>
                                Data de realização
                            </div>
                            <div class="switchContainer">

                                <label class="switch">
                                    @if(isset($colunas['hrealizacao']))
                                    <input type="checkbox" name="colunas[hrealizacao]" checked>
                                    @else
                                    <input type="checkbox" name="colunas[hrealizacao]">
                                    @endif
                                    <span class="slider round"></span>
                                </label>
                                Hora de realização
                            </div>
                            <div class="switchContainer">

                                <label class="switch">
                                    @if(isset($colunas['dregistro']))
                                    <input type="checkbox" name="colunas[dregistro]" checked>
                                    @else
                                    <input type="checkbox" name="colunas[dregistro]">
                                    @endif
                                    <span class="slider round"></span>
                                </label>
                                Data de registro
                            </div>
                            <div class="switchContainer">

                                <label class="switch">
                                    @if(isset($colunas['hregistro']))
                                    <input type="checkbox" name="colunas[hregistro]" checked>
                                    @else
                                    <input type="checkbox" name="colunas[hregistro]">
                                    @endif
                                    <span class="slider round"></span>
                                </label>
                                Hora de registro
                            </div>
                            <div class="switchContainer">

                                <label class="switch">
                                    @if(isset($colunas['ch']))
                                    <input type="checkbox" name="colunas[ch]" checked>
                                    @else
                                    <input type="checkbox" name="colunas[ch]">
                                    @endif
                                    <span class="slider round"></span>
                                </label>
                                Carga horária da atividade
                            </div>
                            <div class="switchContainer">

                                <label class="switch">
                                    @if(isset($colunas['status']))
                                    <input type="checkbox" name="colunas[status]" checked>
                                    @else
                                    <input type="checkbox" name="colunas[status]">
                                    @endif
                                    <span class="slider round"></span>
                                </label>
                                Status
                            </div>

                            <button class="miniBtn" onclick="hideIndexCConfig()" type="submit">OK</button>

                        </div>
                    </div>
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

            <button style="width: 1.5rem; margin-top:25px; margin-right:25px" type="button" class="miniBtn"
                onclick="showIndexCConfig()">
                <img style="margin-top: 2px" src="{{ url('/image/cog.png') }}" width="16" height="16" />
            </button>
        </div>



        {{-- tabela com as informações --}}
        
        <div id="atvGrid"
        @if(isset($colunas))
            style="grid-template-columns: 3.5rem repeat({{ count($colunas) }}{{-- quantidade de colunas --}} , minmax(200px, 1fr));">
        @else
            style="grid-template-columns: 3.5rem repeat({{ 0 }}{{-- quantidade de colunas --}} , minmax(200px, 1fr));">
        @endif
            
            @if (count($atv) > 0)
                <div> <b> ID </b> </div>
                @if(isset($colunas['descricao']))
                <div> <b> Descrição </b> </div>
                @endif
                @if(isset($colunas['usuario']))
                <div> <b> Usuários envolvidos </b> </div>
                @endif
                @if(isset($colunas['requisitante']))
                <div> <b> Requisitante </b> </div>
                @endif
                @if(isset($colunas['drealizacao']))
                <div> <b> Data de realização </b> </div>
                @endif
                @if(isset($colunas['hrealizacao']))
                <div> <b> Hora de realização </b> </div>
                @endif
                @if(isset($colunas['dregistro']))
                <div> <b> Data do registro </b> </div>
                @endif
                @if(isset($colunas['hregistro']))
                <div> <b> Hora do registro </b> </div>
                @endif
                @if(isset($colunas['ch']))
                <div> <b> Carga horária da atividade </b> </div>
                @endif
                @if(isset($colunas['status']))
                <div> <b> Status </b> </div>
                @endif
                @foreach ($atv as $key => $value)
                    <div onclick="location.href='{{ URL::to('atividades/' . $value->atividade_id) }}';">
                        {{ $value->atividade_id }}
                    </div>
                    @if(isset($colunas['descricao']))
                    <div onclick="location.href='{{ URL::to('atividades/' . $value->atividade_id) }}';" class="desc">
                        {{ $value->descricao }}
                    </div>
                    @endif
                    @if(isset($colunas['usuario']))
                    <div onclick="location.href='{{ URL::to('atividades/' . $value->atividade_id) }}';">
                        {{ $value->nome }}
                    </div>
                    @endif
                    @if(isset($colunas['requisitante']))
                    <div onclick="location.href='{{ URL::to('atividades/' . $value->atividade_id) }}';">
                        {{ $value->requisitante }}
                    </div>
                    @endif
                    @if(isset($colunas['drealizacao']))
                    <div onclick="location.href='{{ URL::to('atividades/' . $value->atividade_id) }}';">
                        {{ $value->data_atividade }}
                    </div>
                    @endif
                    @if(isset($colunas['hrealizacao']))
                    <div onclick="location.href='{{ URL::to('atividades/' . $value->atividade_id) }}';">
                        {{ $value->hora_atividade }}
                    </div>
                    @endif
                    @if(isset($colunas['dregistro']))
                    <div onclick="location.href='{{ URL::to('atividades/' . $value->atividade_id) }}';">
                        {{ $value->data_registro }}
                    </div>
                    @endif
                    @if(isset($colunas['hregistro']))
                    <div onclick="location.href='{{ URL::to('atividades/' . $value->atividade_id) }}';">
                        {{ $value->hora_registro }}
                    </div>
                    @endif
                    @if(isset($colunas['ch']))
                    <div onclick="location.href='{{ URL::to('atividades/' . $value->atividade_id) }}';">
                        {{ $value->carga }}
                    </div>
                    @endif
                    @if(isset($colunas['status']))
                    <div onclick="location.href='{{ URL::to('atividades/' . $value->atividade_id) }}';">
                        {{ $value->status }}
                    </div>
                    @endif
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








</body>
<script>
    let filter = (window.location.href).split('=')[1];
    if (filter = 'undefined') {
        filter = '';
    }
    document.getElementById("filter").value = filter;
</script>

</html>
