<!DOCTYPE html>
<html>

<head>
    <title>Atividade ID-{{ $atv->atividade_id }} - TutorLister</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
    </style>
    <script>
        function addField() {

            let htmlUserInput =
                '<div class="form-field" id="involv"><input type="text" name="InvolvedUsers[]" id="InvolvedUsers" list="users"><datalist id="users"><option value=""></option></datalist></div>'
            let doc = new DOMParser().parseFromString(htmlUserInput, "text/html");
            document.getElementById("userContainer").appendChild(doc.firstChild);


        }

        function rmvField() {
            console.log(document.getElementById("userContainer").children.length > 1);
            if (document.getElementById("userContainer").children.length > 1) {
                document.getElementById("userContainer").lastChild.remove;
                document.getElementById("userContainer").removeChild(document.getElementById("userContainer")
                    .lastElementChild)
            }
        }
    </script>
    @php
        
    @endphp
</head>
@include('sidemenu')

<body class="antialiased" id="eventBody">
    <div class="wrapAll">


        <h1>Editar Atividade ID-{{ $atv->atividade_id }}</h1>



        <div class="atvDetalhes" style="height: auto">


            {{ Form::model($atv, ['route' => ['atividades.update', $atv->atividade_id], 'method' => 'PUT', 'class' => 'atvForm', 'autocomplete' => 'off']) }}


            <div>
                <div id="repart2">
                    <div>

                        <div style="display: flex;">

                            Alunos envolvidos

                        </div>
                        {{ Html::ul($errors->get('InvolvedUsers'), ['class' => 'ulError']) }}
                        <div id="userContainer">

                            @if (null !== old('InvolvedUsers'))
                                @foreach (old('InvolvedUsers') as $user)
                                    <div style="display: grid; grid-template-columns: 95% auto">
                                        <div class="form-field" id="involv">
                                            <input type="text" name="InvolvedUsers[]" id="usuario" class="usuario"
                                                list="users" value="{{ $user }}">
                                            @include('autocomplete', ['campo' => '.usuario'])
                                        </div>
                                        <a href="javascript:void(0)" class="add_button" title="add field"><img
                                                style="margin: auto; margin-top: 0"
                                                src="{{ url('/image/mais.png') }}" /></a>
                                    </div>
                                @endforeach
                            @else
                                @foreach ($atv->usuarios as $key => $value)
                                    <div style="display: grid; grid-template-columns: 95% auto">
                                        <div class="form-field" id="involv">
                                            <input type="text" name="InvolvedUsers[]" id="usuario" class="usuario"
                                                list="users" value='{{ $value->nome }}'>
                                            @include('autocomplete', ['campo' => '.usuario'])
                                        </div>
                                        <a href="javascript:void(0)" class="add_button" title="add field"><img
                                                style="margin: auto; margin-top: 0"
                                                src="{{ url('/image/mais.png') }}" /></a>
                                    </div>
                                @endforeach

                            @endif
                        </div>
                    </div>
                    <div>

                        <span>Requisitante</span>

                        {{ Html::ul($errors->get('Requisitante'), ['class' => 'ulError']) }}
                        <div style="display: grid; grid-template-columns: auto 5rem">
                            <div class="form-field" id="req"> <span></span>
                                @if (old('Requisitante') !== null)
                                    <input type="text" name="Requisitante" id="Requisitante"
                                        value="{{ old('Requisitante') }}" list="users">
                                @else
                                    <input type="text" name="Requisitante" id="Requisitante" list="reqs"
                                        value={{ $atv->requisitante->nome }}>
                                @endif
                                @include('autocomplete', ['campo' => '#Requisitante'])
                            </div>
                            <button class="dt-button mt-3" type="button" style="margin-bottom: 20px;">
                                <a style="text-decoration: none; color: black" target="_blank"
                                    href="http://localhost:8000/requisitantes/create">
                                    Novo
                                </a>
                            </button>
                        </div>
                    </div>

                </div>




                <div id="repart3">
                    <span>Data da atividade</span>
                    <span>Hora da atividade</span>
                    <span>Carga Horária</span>
                    <div>
                        {{ Html::ul($errors->get('DoneData'), ['class' => 'ulError']) }}
                        <div class="form-field dth"> <span></span>
                            @if (old('DoneData') !== null)
                                <input type="date" name="DoneData" id="DoneData" value="{{ old('DoneData') }}"
                                    max="{{ date('Y-m-d') }}">
                            @else
                                <input type="date" name="DoneData" id="DoneData" value={{ $atv->data_atividade }}
                                    max="{{ date('Y-m-d') }}">
                            @endif
                        </div>
                    </div>

                    <div>
                        {{ Html::ul($errors->get('DoneHour'), ['class' => 'ulError']) }}
                        <div class="form-field dth"> <span></span>
                            @if (old('DoneHour') !== null)
                                <input type="time" name="DoneHour" id="DoneHour" value="{{ old('DoneHour') }}">
                            @else
                                <input type="time" name="DoneHour" id="DoneHour" value={{ $atv->hora_atividade }}>
                            @endif
                        </div>
                    </div>

                    <div>
                        {{ Html::ul($errors->get('CargaHoraria'), ['class' => 'ulError']) }}
                        <div class="form-field" id="ch"> <span></span>
                            @if (old('CargaHoraria') !== null)
                                <input type="time" name="CargaHoraria" id="CargaHoraria"
                                    value="{{ old('CargaHoraria') }}">
                            @else
                                <input type="time" name="CargaHoraria" id="CargaHoraria" value={{ $atv->carga }}>
                            @endif
                        </div>
                    </div>
                    <div>
                        <span>Status</span>
                        <div class="form-field" id="ch"> <span></span>

                            <select name="status" id="status">
                                @if ($atv->status == 'Aberto')
                                    <option value="Aberto" selected>Aberto</option>
                                    <option value="Em andamento">Em andamento</option>
                                    <option value="Fechado">Fechado</option>
                                    <option value="Cancelado">Cancelado</option>
                                @elseif($atv->status == 'Em andamento')
                                    <option value="Aberto">Aberto</option>
                                    <option value="Em andamento" selected>Em andamento</option>
                                    <option value="Fechado">Fechado</option>
                                    <option value="Cancelado">Cancelado</option>
                                @elseif($atv->status == 'Fechado')
                                    <option value="Aberto">Aberto</option>
                                    <option value="Em andamento">Em andamento</option>
                                    <option value="Fechado" selected>Fechado</option>
                                    <option value="Arquivado">Arquivado</option>
                                    <option value="Cancelado">Cancelado</option>
                                @elseif($atv->status == 'Cancelado')
                                    <option value="Aberto">Aberto</option>
                                    <option value="Em andamento">Em andamento</option>
                                    <option value="Fechado">Fechado</option>
                                    <option value="Arquivado">Arquivado</option>
                                    <option value="Cancelado" selected>Cancelado</option>
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div>

                Descrição
                {{ Html::ul($errors->get('descricao'), ['class' => 'ulError']) }}
                <div class="form-field formDesc"> <span></span>
                    @if (old('descricao') !== null)
                        <textarea name="descricao" id="descricao" cols="30" rows="10" class="desc">{{ old('descricao') }} </textarea>
                    @else
                        <textarea name="descricao" id="descricao" cols="30" rows="10" class="desc">{{ $atv->descricao }} </textarea>
                    @endif
                </div>
            </div>

            <div class="atvFormBtn">

                <div>

                    <a onclick="return confirm('Cancelar edição?')"
                        href="{{ url('atividades/' . $atv->atividade_id) }}">
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
