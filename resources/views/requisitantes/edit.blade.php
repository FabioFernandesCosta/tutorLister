<!DOCTYPE html>
<html>

<head>
    <title>Atividade ID-{{ $requisitante->usuario_id }} - TutorLister</title>
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


        <h1>Editar Requisitante: {{ $requisitante->nome }}</h1>
        <h3>ID-{{ $requisitante->requisitante_id }}</h3>



        <div class="atvDetalhes" style="height: auto">


            {{ Form::model($requisitante, ['route' => ['requisitantes.update', $requisitante->requisitante_id], 'method' => 'PUT', 'class' => 'atvForm', 'autocomplete' => 'off']) }}


            {{-- https://stackoverflow.com/questions/35332784/how-to-call-a-controller-function-inside-a-view-in-laravel-5 --}}


            <div>
                <div>
                    {{-- botões --}}
                    {{-- Inputs --}}
                    <div id="repart2">
                        <div>
                            <span>Nome</span>
                            {{ Html::ul($errors->get('nome'), ['class' => 'ulError']) }}
                            <div class="form-field form-field-little" id="req"> <span></span>
                                {{-- nome input value if old is not null and if is null --}}
                                <input type="text" value="{{ old('nome') ?? $requisitante->nome }}" name="nome"
                                    id="usuario">
                            </div>
                            <div>
                                <span>Empresa</span>
                                {{ Html::ul($errors->get('empresa'), ['class' => 'ulError']) }}
                                <div class="form-field form-field-littlePlus" id="ac"> <span></span>
                                    {{-- acesso select default value if old is not null and if is null --}}
                                    <input type="text" value="{{ old('empresa') ?? $requisitante->empresa }}"
                                        name="empresa" id="empresa">
                                </div>
                                <script>
                                    var route = "{{ url('requisitantes/consultar') }}";
                                    $("#empresa").typeahead({
                                        source: function(query, process) {
                                            return $.get(route, {
                                                term: "empresa_" + query
                                            }, function(data) {
                                                return process(data);
                                            });
                                        }
                                    });
                                </script>
                            </div>


                        </div>
                        <div id="userContainer">
                            E-mail
                            {{ Html::ul($errors->get('email'), ['class' => 'ulError']) }}
                            <div class="form-field form-field-little" id="req"> <span></span>
                                {{-- email input value if old is not null and if is null --}}
                                <input type="text" value="{{ old('email') ?? $requisitante->email }}" name="email"
                                    id="email" required>
                            </div>
                            Telefone
                            {{ Html::ul($errors->get('telefone'), ['class' => 'ulError']) }}
                            <div class="form-field form-field-little" id="req"> <span></span>
                                {{-- telefone input value if old is not null and if is null --}}
                                <input type="tel" value="{{ old('telefone') ?? $requisitante->telefone }}" name="telefone"
                                    id="telefone" required pattern="[0-9]{2} [0-9]{9}" placeholder="43 123456789">
                            </div>

                        </div>


                    </div>

                </div>
            </div>


            <div class="atvFormBtn">

                <div>

                    <a onclick="return confirm('Cancelar edição?')" href="{{ url('requisitantes/' . $requisitante->requisitante_id) }}">
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

</html>>
{{-- https://stackoverflow.com/questions/44517785/php-laravel-html-forms-array-of-values --}}
