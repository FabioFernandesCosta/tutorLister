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

            {{ Form::open(['url' => 'alunos', 'class' => 'atvForm', 'autocomplete' => 'off', 'action' => 'alunosController@store']) }}

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
                            <input type="text" value="{{ old('nome') }}" name="nome" required='required'
                                id="usuario">
                        </div>
                        <div>
                            <span>Níveis de acesso ao sistema</span>
                            {{ Html::ul($errors->get('acesso'), ['class' => 'ulError']) }}
                            <div class="form-field form-field-littlePlus" id="ac"> <span></span>
                                <select name="acesso" required='required' id="acesso">
                                    <option value="1">NPI</option>
                                    <option value="2">Aluno Tutor</option>
                                    <option value="3">NPI e Aluno Tutor</option>
                                    <option selected value="0">Nenhum</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <span>Está no NPI?</span>
                            {{ Html::ul($errors->get('npi'), ['class' => 'ulError']) }}
                            <div class="form-field form-field-littlePlus" id="np"> <span></span>
                                <select name="npi" required='required' id="npi">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </div>
                        </div>
                        @if (Auth::user()->npi == 1)
                            <div>
                                <span>Está no Aluno Tutor?</span>
                                {{ Html::ul($errors->get('aluno_tutor'), ['class' => 'ulError']) }}
                                <div class="form-field form-field-littlePlus" id="at"> <span></span>
                                    <select name="aluno_tutor" required='required' id="aluno_tutor">
                                        <option value="0">Não</option>
                                        <option value="1">Sim</option>
                                    </select>
                                </div>
                            </div>
                        @endif

                        @if (Auth::user()->aluno_tutor == 1)
                            <div>
                                <span>Concluiu treinamento?</span>
                                {{ Html::ul($errors->get('treinamento_concluido'), ['class' => 'ulError']) }}
                                <div class="form-field form-field-littlePlus" id="at"> <span></span>
                                    <select name="treinamento_concluido" required='required' id="treinamento_concluido">
                                        <option value="1">NPI</option>
                                        <option value="2">Aluno Tutor</option>
                                        <option value="3">NPI e Aluno Tutor</option>
                                        <option selected value="0">Nenhum</option>

                                    </select>
                                </div>
                            </div>
                        @endif

                        <span>Curso</span>
                        {{-- {{ Html::ul($errors->get('Requisitante'), ['class' => 'ulError']) }} --}}
                        {{-- erros --}}
                        {{ Html::ul($errors->get('curso'), ['class' => 'ulError']) }}
                        <div class="form-field form-field-little" id="req"> <span></span>
                            <input type="text" value="{{ old('curso') }}" name="curso" required='required'
                                id="curso">
                        </div>
                        @include('autocomplete', ['campo' => '#curso'])

                        <div>
                            <span>Horário</span>
                            {{ Html::ul($errors->get('horario'), ['class' => 'ulError']) }}

                            <div class="form-field form-field-littlePlus" id="at"> <span></span>
                                <select name="horario" required='required' id="horario">
                                    <option value="Manhã" {{ old('horario') == 'Manhã' ? 'selected' : '' }}>Manhã
                                    </option>
                                    <option value="Tarde" {{ old('horario') == 'Tarde' ? 'selected' : '' }}>Tarde
                                    </option>
                                    <option value="Noite" {{ old('horario') == 'Noite' ? 'selected' : '' }}>Noite
                                    </option>
                                </select>
                            </div>

                        </div>



                    </div>
                    <div id="userContainer">
                        E-mail
                        {{ Html::ul($errors->get('email'), ['class' => 'ulError']) }}
                        <div class="form-field form-field-little" id="req"> <span></span>
                            <input type="email" value="{{ old('email') }}" name="email" required='required'
                                id="email">
                        </div>
                        Telefone
                        {{ Html::ul($errors->get('telefone'), ['class' => 'ulError']) }}
                        <div class="form-field form-field-little" id="req"> <span></span>
                            <input type="tel" value="{{ old('telefone') }}" name="telefone" required='required'
                                id="telefone" pattern="[0-9]{2} [0-9]{9}" placeholder="43 123456789">
                        </div>

                    </div>
                </div>
            </div>


            <div class="atvFormBtn">
                <div>

                    <a href="{{ url('alunos') }}">
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
