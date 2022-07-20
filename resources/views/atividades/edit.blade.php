<!DOCTYPE html>
<html>

<head>
    <title>Atividade ID-{{ $atv->atividade_id }} - TutorLister</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
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
                document.getElementById("userContainer").removeChild(document.getElementById("userContainer").lastElementChild)
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


            {{-- https://stackoverflow.com/questions/35332784/how-to-call-a-controller-function-inside-a-view-in-laravel-5 --}}
            <div>

                Descrição
                {{ Html::ul($errors->get('descricao'), ['class' => 'ulError']) }}
                <div class="form-field formDesc"> <span></span>
                @if(old('descricao') !== null)
                    <textarea name="descricao" id="descricao" cols="30" rows="10" class="desc">{{ old('descricao') }} </textarea>
                @else
                    <textarea name="descricao" id="descricao" cols="30" rows="10" class="desc">{{ $atv->descricao }} </textarea>
                @endif
                </div>
            </div>

            <div>
                <div style="display: flex;">

                    Usuarios envolvidos <button type="button" onclick="addField()" class="miniBtn"
                        style="margin-top: 0; margin-left:5px; height:20px">add</button><button type="button"
                        onclick="rmvField()" class="miniBtn" style="margin-top: 0;height:20px">rmv</button>

                </div>
                {{ Html::ul($errors->get('InvolvedUsers'), ['class' => 'ulError']) }}
                <div id="userContainer">
                    @if (null !== old('InvolvedUsers'))
                        @foreach (old('InvolvedUsers') as $user)
                            <div class="form-field" id="involv">
                                    <input type="text" name="InvolvedUsers[]" id="usuario" class="usuario"
                                            list="users" value="{{ $user }}">
                                        @include('autocomplete', ['campo' => '.usuario'])
                            </div>
                        @endforeach
                    @else
                        @foreach ($atv->usuarios as $key => $value)
                            <div class="form-field" id="involv">
                                <input type="text" name="InvolvedUsers[]" id="usuario" class="usuario"
                                    list="users" value='{{ $value->nome }}'>
                                @include('autocomplete', ['campo' => '.usuario'])
                            </div>
                        @endforeach

                    @endif
                </div>

                <span>Requisitante</span>

                {{ Html::ul($errors->get('Requisitante'), ['class' => 'ulError']) }}
                <div class="form-field" id="req"> <span></span>
                    @if(old('Requisitante') !== null)
                        <input type="text" name="Requisitante" id="Requisitante" value="{{ old('Requisitante') }}" list="users">
                    @else
                        <input type="text" name="Requisitante" id="Requisitante" list="reqs" value={{ $atv->requisitante->nome }}>
                    @endif
                    @include('autocomplete', ['campo' => '#Requisitante'])
                </div>



                <div id="repart3">
                    <span>Data da atividade</span>
                    <span>Hora da atividade</span>
                    <span>Carga Horária</span>
                    <div>
                        {{ Html::ul($errors->get('DoneData'), ['class' => 'ulError']) }}
                        <div class="form-field dth"> <span></span>
                            @if(old('DoneData') !== null)
                                <input type="date" name="DoneData" id="DoneData" value="{{ old('DoneData') }}">
                            @else
                            <input type="date" name="DoneData" id="DoneData" value={{ $atv->data_atividade }}>
                            @endif
                        </div>
                    </div>

                    <div>
                        {{ Html::ul($errors->get('DoneHour'), ['class' => 'ulError']) }}
                        <div class="form-field dth"> <span></span>
                            @if(old('DoneHour') !== null)
                                <input type="time" name="DoneHour" id="DoneHour" value="{{ old('DoneHour') }}">
                            @else
                            <input type="time" name="DoneHour" id="DoneHour" value={{ $atv->hora_atividade }}>
                            @endif
                        </div>
                    </div>

                    <div>
                        {{ Html::ul($errors->get('CargaHoraria'), ['class' => 'ulError']) }}
                        <div class="form-field" id="ch"> <span></span>
                            @if(old('CargaHoraria') !== null)
                                
                                <input type="time" name="CargaHoraria" id="CargaHoraria" value="{{ old('CargaHoraria') }}">
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
                                @elseif($atv->status == 'Em andamento')
                                    <option value="Aberto">Aberto</option>
                                    <option value="Em andamento" selected>Em andamento</option>
                                    <option value="Fechado">Fechado</option>
                                @elseif($atv->status == 'Fechado')
                                    <option value="Aberto">Aberto</option>
                                    <option value="Em andamento">Em andamento</option>
                                    <option value="Fechado" selected>Fechado</option>
                                    <option value="Arquivado">Arquivado</option>
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="atvFormBtn">

                {{ Form::submit('Editar atividade', ['class' => 'btn mt-3', 'style' => 'width: 12rem; margin-top: 45px']) }}
                <a href="{{ url('atividades/' . $atv->atividade_id) }}">
                    <button type="button" class="btn" style="margin-top: 45px; width:8rem">Cancelar</button>
                </a>
            </div>
            {{ Form::close() }}
        </div>




    </div>
</body>

</html>>
{{-- https://stackoverflow.com/questions/44517785/php-laravel-html-forms-array-of-values --}}
