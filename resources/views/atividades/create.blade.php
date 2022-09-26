<!DOCTYPE html>
<html>

<head>
    <title>Nova Atividade - TutorLister</title>
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


        <h1>Registrar Atividade</h1>

        <!-- if there are creation errors, they will show here -->

        <div class="atvDetalhes" style="height: auto">

            {{ Form::open(['url' => 'atividades', 'class' => 'atvForm', 'autocomplete' => 'off', 'action' => 'atividadeController@store']) }}



            

            <div>
                {{-- botões --}}
                {{-- Inputs --}}
                {{ Html::ul($errors->get('InvolvedUsers'), ['class' => 'ulError']) }}
                <div id="repart2">
                        
                        <div id="userContainer">
                            Usuarios envolvidos
                            
                            @if (null !== old('InvolvedUsers'))
                                @foreach (old('InvolvedUsers') as $user)
                                    <div style="display: grid; grid-template-columns: 95% auto">
                                    
                                        <div class="form-field form-field-little" id="involv">
                                            <input type="text" class="usuario" required='required' name="InvolvedUsers[0]"
                                                value="{{ $user }}" id="usuario" list="users">
                                        </div>
                                        <a href="javascript:void(0)" class="add_button" title="add field"><img style="margin: auto; margin-top: 0" src="{{url('/image/mais.png')}}" /></a>
                                    </div>
                                @endforeach
                            @else
                            <div style="display: grid; grid-template-columns: 95% auto">
                                <div class="form-field form-field-little" id="involv">
                                    <input type="text" class="usuario" required='required' name="InvolvedUsers[0]"
                                        value="{{ old('InvolvedUsers.0') }}" id="usuario" list="users">
                                    </div>
                                    <a href="javascript:void(0)" class="add_button" title="add field"><img style="margin: auto; margin-top: 0" src="{{url('/image/mais.png')}}" /></a>
                            </div>
                            @endif
        
                        </div>
    
                    <div>
                        <span>Requisitante</span>
                        {{ Html::ul($errors->get('Requisitante'), ['class' => 'ulError']) }}
                        <div class="form-field form-field-little" id="req"> <span></span>
                            <input type="text" value="{{ old('Requisitante') }}" name="Requisitante"
                                required='required' id="Requisitante" list="reqs">
                        </div>
    
    
    
    
    
                    </div>
                </div>

                <div id="repart3">
                    <span>Data da atividade</span>
                    <span>Hora da atividade</span>
                    <span>Carga horária</span>
                    <div>
                        {{ Html::ul($errors->get('DoneData'), ['class' => 'ulError']) }}
                        <div class="form-field form-field-littlePlus dth"> <span></span>
                            <input type="date" required='required' name="DoneData" value="{{ old('DoneData') }}" max="{{ date('Y-m-d') }}"
                                id="DoneData">
                        </div>
                    </div>

                    <div>
                        {{ Html::ul($errors->get('DoneHour'), ['class' => 'ulError']) }}
                        <div class="form-field form-field-littlePlus dth"> <span></span>
                            <input type="time" required='required' name="DoneHour" value="{{ old('DoneHour') }}"
                                id="DoneHour">
                        </div>
                    </div>


                    <div>
                        {{ Html::ul($errors->get('CargaHoraria'), ['class' => 'ulError']) }}
                        <div class="form-field form-field-littlePlus" id="ch"> <span></span>
                            @if (null !== old('CargaHoraria'))
                                <input type="time" required='required' name="CargaHoraria"
                                    value="{{ old('CargaHoraria') }}" id="CargaHoraria">
                            @else
                                <input type="time" required='required' name="CargaHoraria" id="CargaHoraria"
                                    value="00:00">
                            @endif
                        </div>
                    </div>

                    <div>
                        <span>Status</span>
                        <div class="form-field form-field-littlePlus" id="ch"> <span></span>
                            <select name="status" required='required' id="status">
                                <option value="Aberto">Aberto</option>
                                <option value="Em andamento">Em andamento</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div>

                Descrição
                {{ Html::ul($errors->get('descricao'), ['class' => 'ulError']) }}
                <div class="form-field formDesc"> <span></span>
                    <textarea name="descricao" required="required" id="descricao" cols="30" rows="10" class="desc">{{ old('descricao') }}</textarea>
                </div>
            </div>

            <div class="atvFormBtn">
                <div>

                    <a href="{{ url('atividades') }}">
                        <button type="button" class="btn dt-button" onclick="return confirm('Cancelar')" style="margin-top: 45px;">Cancelar</button>
                    </a>
                </div>
                <div class="btn-right">

                    {{ Form::submit('Registrar', ['class' => 'btn mt-3 dt-button', 'style' =>  'margin-top: 45px', 'onclick'=>"return confirm('Confirmar?');"]) }}
                </div>
            </div>

            {{ Form::close() }}
        </div>




    </div>
    <a href=""></a>

</body>
@include('autocomplete', ['campo' => '.usuario'])
@include('autocomplete', ['campo' => '#Requisitante'])


<script>
        

    $(document).ready(function() {
        var maxfields = 10;
        var wrapper = $("#userContainer");
        var add_button = $(".add_button");

        var x = 1;
        var userInput = `<input type="text" class="usuario" required='required' name="InvolvedUsers[0]" id="usuario" list="users">`;
        
        $(add_button).click(function() {
            typeaheadInit();
            if (x < maxfields) {
                x++;
                var involvHTML = `<div style="display: grid; grid-template-columns: 95% auto"><div class="form-field form-field-little" id="involv`+ x + `"> </div> <a href="javascript:void(0)" class="rmv_button" title="add field"><img style="margin: auto; margin-top: 0" src="{{url('/image/menos.png')}}" /></a> </div>`;
                //$(wrapper).append(involvHTML);
                $(involvHTML).appendTo(wrapper);
                $(userInput).appendTo("#involv"+x).typeahead({
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
