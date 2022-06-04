<!DOCTYPE html>
<html>

<head>
    <title>Shark App</title>
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
            console.log(document.getElementById("userContainer").children.length > 1);
            if (document.getElementById("userContainer").children.length > 1) {
                document.getElementById("userContainer").lastChild.remove;
                document.getElementById("userContainer").removeChild(document.getElementById("userContainer")
                    .lastElementChild)
            }
        }
    </script>

</head>
@include('sidemenu')

<body class="antialiased" id="eventBody">
    <div class="wrapAll">


        <h1>Registrar Atividade</h1>

        <!-- if there are creation errors, they will show here -->
        {{ Html::ul($errors->all()) }}

        <div class="atvDetalhes" style="height: auto">

            {{ Form::open(['url' => 'atividades', 'class' => 'atvForm', 'autocomplete' => 'off', 'action' => 'atividadeController@store']) }}



            <div>

                Descrição
                <div class="form-field desc"> <span></span>

                    <textarea name="descricao" id="descricao" cols="30" rows="10" class="desc"></textarea>
                </div>
            </div>

            <div>
                <div style="display: flex;">

                    Usuarios envolvidos <button type="button" onclick="addField()" class="miniBtn"
                        style="margin-top: 0; margin-left:5px; height:20px">add</button><button type="button"
                        onclick="rmvField()" class="miniBtn" style="margin-top: 0;height:20px">rmv</button>
                </div>
                <div id="userContainer">

                    <div class="form-field" id="involv">
                        <input type="text" class="usuario" name="InvolvedUsers[]" id="usuario" list="users">
                    </div>
                    
                </div>
                
                <div>
                    <span>Requisitante</span>
                    <div class="form-field" id="req"> <span></span>
                        <input type="text" name="Requisitante" id="Requisitante" list="reqs">
                    </div>


                    


                </div>

                <div id="repart3">
                    <span>Data da atividade</span>
                    <span>Hora da atividade</span>
                    <span>Carga Horária</span>
                    <div class="form-field dth"> <span></span>
                        <input type="date" name="DoneData" id="DoneData">
                    </div>
                    <div class="form-field dth"> <span></span>
                        <input type="time" name="DoneHour" id="DoneHour">
                    </div>
                    <div class="form-field" id="ch"> <span></span>
                        <input type="time" name="CargaHoraria" id="CargaHoraria" value="00:00">
                    </div>
                    <div>
                        <span>Status</span>
                        <div class="form-field" id="ch"> <span></span>
                            <select name="status" id="status">
                                <option value="Aberto">Aberto</option>
                                <option value="Em andamento">Em andanmento</option>
                                <option value="Fechado">Fechado</option>
                                <option value="Arquivado">Arquivado</option>
                              </select>
                        </div>
                    </div>
                </div>
            </div>

            {{ Form::submit('Criar atividade!', ['class' => 'btn mt-3', 'style' => 'width: 12rem; margin-top: 45px']) }}

            {{ Form::close() }}
        </div>

        


    </div>
</body>
@include('autocomplete', ['campo' => '.usuario'])
@include('autocomplete', ['campo' => '#Requisitante'])
</html>

{{-- https://stackoverflow.com/questions/44517785/php-laravel-html-forms-array-of-values --}}
