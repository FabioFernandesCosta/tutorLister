<!DOCTYPE html>
<html>

<head>
    <title>Novo Aluno - TutorLister</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
    </style>





</head>
@include('sidemenu')

<body class="antialiased" id="eventBody">
    <div class="wrapAll">


        <h1>Registrar Aluno</h1>

        <!-- if there are creation errors, they will show here -->
        {{-- show all errors --}}
        {{-- {{ Html::ul($errors->all(), ['class' => 'ulError']) }} --}}
        <div class="atvDetalhes" style="height: auto">

            {{ Form::open(['url' => 'cursos', 'class' => 'atvForm', 'autocomplete' => 'off', 'action' => 'cursoController@store']) }}

            <div>
                {{-- botões --}}
                {{-- Inputs --}}
                {{-- {{ Html::ul($errors->get('InvolvedUsers'), ['class' => 'ulError']) }} --}}
                <div id="repart2">
                    <div>
                        <span>Nome</span>
                        {{-- {{ Html::ul($errors->get('Requisitante'), ['class' => 'ulError']) }} --}}
                        {{-- erros --}}
                        {{ Html::ul($errors->get('nome'), ['class' => 'ulError']) }}
                        <div class="form-field form-field-little" id="req"> <span></span>
                            <input type="text" value="{{ old('nome') }}" name="nome" required='required' id="usuario">
                        </div>

                    </div>
                    <div id="userContainer">
                        Area do curso
                        {{ Html::ul($errors->get('area_curso'), ['class' => 'ulError']) }}
                        <div class="form-field form-field-little" id="req"> <span></span>
                            <input type="text" value="{{ old('area_curso') }}" name="area_curso" required='required' id="area_curso">
                        </div>
                    </div>
                </div>
            </div>
            

            <div class="atvFormBtn">
                <div>

                    <a href="{{ url('cursos') }}">
                        <button type="button" class="btn dt-button" onclick="return confirm('Cancelar')"
                            style="margin-top: 45px;">Cancelar</button>
                    </a>
                </div>
                <div class="btn-right">

                    {{ Form::submit('Registrar', ['class' => 'btn mt-3 dt-button', 'style' => 'margin-top: 45px', 'onclick' => "return confirm('Confirmar?');"]) }}
                </div>
            </div>

            {{ Form::close() }}

        </div>
    </div>
    @include('footer')

</body>




<script>
    $(document).ready(function() {
        var maxfields = 10;
        var wrapper = $("#userContainer");
        var add_button = $(".add_button");

        var x = 1;
        var userInput =
            `<input type="text" class="usuario" required='required' name="InvolvedUsers[0]" id="usuario" list="users">`;

        $(add_button).click(function() {
            typeaheadInit();
            if (x < maxfields) {
                x++;
                var involvHTML =
                    `<div style="display: grid; grid-template-columns: 95% auto"><div class="form-field form-field-little" id="involv` +
                    x +
                    `"> </div> <a href="javascript:void(0)" class="rmv_button" title="add field"><img style="margin: auto; margin-top: 0" src="{{ url('/image/menos.png') }}" /></a> </div>`;
                //$(wrapper).append(involvHTML);
                $(involvHTML).appendTo(wrapper);
                $(userInput).appendTo("#involv" + x).typeahead({
                    source: function(query, process) {
                        return $.get('/consultar', {
                            term: ".usuario_" + query
                        }, function(data) {
                            return process(data);
                        });
                    }
                });
            }
        });


        $(wrapper).on("click", ".rmv_button", function(e) {
            e.preventDefault();
            $(this).parent('div').remove();
            x--;
        })


    });
</script>

</html>

{{-- https://stackoverflow.com/questions/44517785/php-laravel-html-forms-array-of-values --}}
