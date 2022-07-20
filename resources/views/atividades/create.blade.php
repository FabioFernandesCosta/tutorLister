<!DOCTYPE html>
<html>

<head>
    <title>Nova Atividade - TutorLister</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
    </style>



    <script>
        function addField() {

            let htmlUserInput =
                '<div class="form-field" id="involv"><input type="text" name="InvolvedUsers[]" class="usuario" list="users"></div>';
            let doc = new DOMParser().parseFromString(htmlUserInput, "text/html");
            document.getElementById("userContainer").appendChild(doc.firstChild.lastChild.firstChild);
            typeaheadInit();


        }

        function rmvField() {
            if (document.getElementById("userContainer").children.length > 1) {
                document.getElementById("userContainer").lastChild.remove;
                document.getElementById("userContainer").removeChild(document.getElementById("userContainer").lastElementChild)
            }
        }
    </script>

</head>
@include('sidemenu')

<body class="antialiased" id="eventBody">
    <div class="wrapAll">


        <h1>Registrar Atividade</h1>

        <!-- if there are creation errors, they will show here -->

        <div class="atvDetalhes" style="height: auto">

            {{ Form::open(['url' => 'atividades', 'class' => 'atvForm', 'autocomplete' => 'off', 'action' => 'atividadeController@store']) }}



            <div>

                Descrição
                {{ Html::ul($errors->get('descricao'), ['class' => 'ulError']) }}
                <div class="form-field formDesc"> <span></span>
                    <textarea name="descricao" required="required" id="descricao" cols="30" rows="10" class="desc">{{ old('descricao') }}</textarea>
                </div>
            </div>

            <div>
                {{-- botões --}}
                <div style="display: flex;">

                    Usuarios envolvidos <button type="button" onclick="addField()" class="miniBtn"
                        style="margin-top: 0; margin-left:5px; height:20px">adicionar</button><button type="button"
                        onclick="rmvField()" class="miniBtn" style="margin-top: 0;height:20px">remover</button>
                </div>
                {{-- Inputs --}}
                {{ Html::ul($errors->get('InvolvedUsers'), ['class' => 'ulError']) }}
                <div id="userContainer">

                    @if (null !== old('InvolvedUsers'))
                        @foreach (old('InvolvedUsers') as $user)
                            <div class="form-field" id="involv">
                                <input type="text" class="usuario" required='required' name="InvolvedUsers[0]"
                                    value="{{ $user }}" id="usuario" list="users">
                            </div>
                        @endforeach
                    @else
                        <div class="form-field" id="involv">
                            <input type="text" class="usuario" required='required' name="InvolvedUsers[0]"
                                value="{{ old('InvolvedUsers.0') }}" id="usuario" list="users">
                        </div>
                    @endif

                </div>

                <div>
                    <span>Requisitante</span>
                    {{ Html::ul($errors->get('Requisitante'), ['class' => 'ulError']) }}
                    <div class="form-field" id="req"> <span></span>
                        <input type="text" value="{{ old('Requisitante') }}" name="Requisitante"
                            required='required' id="Requisitante" list="reqs">
                    </div>





                </div>

                <div id="repart3">
                    <span>Data da atividade</span>
                    <span>Hora da atividade</span>
                    <span>Carga horária</span>
                    <div>
                        {{ Html::ul($errors->get('DoneData'), ['class' => 'ulError']) }}
                        <div class="form-field dth"> <span></span>
                            <input type="date" required='required' name="DoneData" value="{{ old('DoneData') }}"
                                id="DoneData">
                        </div>
                    </div>

                    <div>
                        {{ Html::ul($errors->get('DoneHour'), ['class' => 'ulError']) }}
                        <div class="form-field dth"> <span></span>
                            <input type="time" required='required' name="DoneHour" value="{{ old('DoneHour') }}"
                                id="DoneHour">
                        </div>
                    </div>


                    <div>
                        {{ Html::ul($errors->get('CargaHoraria'), ['class' => 'ulError']) }}
                        <div class="form-field" id="ch"> <span></span>
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
                        <div class="form-field" id="ch"> <span></span>
                            <select name="status" required='required' id="status">
                                <option value="Aberto">Aberto</option>
                                <option value="Em andamento">Em andanmento</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="atvFormBtn">

                {{ Form::submit('Criar atividade', ['class' => 'btn mt-3', 'style' => 'width: 12rem; margin-top: 45px']) }}
                <a href="{{ url('atividades') }}">
                    <button type="button" class="btn" style="margin-top: 45px; width:8rem">Cancelar</button>
                </a>
            </div>

            {{ Form::close() }}
        </div>




    </div>

</body>
@include('autocomplete', ['campo' => '.usuario'])
@include('autocomplete', ['campo' => '#Requisitante'])

</html>

{{-- https://stackoverflow.com/questions/44517785/php-laravel-html-forms-array-of-values --}}
