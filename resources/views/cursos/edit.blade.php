<!DOCTYPE html>
<html>

<head>
    <title>Curso ID-{{ $curso->curso_id }} - TutorLister</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
    </style>

</head>
@include('sidemenu')

<body class="antialiased" id="eventBody">
    <div class="wrapAll">


        <h1>Editar Curso: {{ $curso->nome }}</h1>
        <h3>ID-{{ $curso->usuario_id }}</h3>



        <div class="atvDetalhes" style="height: auto">


            {{ Form::model($curso, ['route' => ['cursos.update', $curso->curso_id], 'method' => 'PUT', 'class' => 'atvForm', 'autocomplete' => 'off']) }}


            <div>
                <div>
                    {{-- botões --}}
                    {{-- Inputs --}}
                    <div id="repart2">
                        <div>
                            <span>Nome</span>
                            {{-- {{ Html::ul($errors->get('Requisitante'), ['class' => 'ulError']) }} --}}
                            {{-- erros --}}
                            {{ Html::ul($errors->get('nome'), ['class' => 'ulError']) }}
                            <div class="form-field form-field-little" id="req"> <span></span>
                                <input type="text" value="{{ old('nome') ?? $curso->nome }}" name="nome"
                                    required='required' id="usuario">
                            </div>

                        </div>
                        <div id="userContainer">
                            Area do curso
                            {{ Html::ul($errors->get('area_curso'), ['class' => 'ulError']) }}
                            <div class="form-field form-field-little" id="req"> <span></span>
                                <input type="text" value="{{ old('area_curso') ?? $curso->area_curso }}"
                                    name="area_curso" required='required' id="area_curso">
                            </div>
                        </div>
                    </div>

                </div>
            </div>


            <div class="atvFormBtn">

                <div>

                    <a onclick="return confirm('Cancelar edição?')" href="{{ url('cursos/' . $curso->curso_id) }}">
                        <button type="button" class="btn dt-button outline-btn"
                            style="margin-top: 45px;">Cancelar</button>
                    </a>
                </div>
                <div class="btn-right">

                    {{ Form::submit('Salvar', ['class' => 'btn mt-3 dt-button', 'style' => 'margin-top: 45px', 'onclick' => "return confirm('Confirmar edição?');"]) }}

                </div>
            </div>
            {{ Form::close() }}
        </div>




    </div>

    @include('footer')
</body>

</html>
{{-- https://stackoverflow.com/questions/44517785/php-laravel-html-forms-array-of-values --}}
