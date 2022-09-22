<!DOCTYPE html>
<html>

<head>
    <title>Atividade ID-{{ $aluno->usuario_id }} - TutorLister</title>
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


        <h1>Editar Aluno ID-{{ $aluno->usuario_id }}</h1>


        {{-- list all errors --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="atvDetalhes" style="height: auto">


            {{ Form::model($aluno, ['route' => ['alunos.update', $aluno->usuario_id], 'method' => 'PUT', 'class' => 'atvForm', 'autocomplete' => 'off']) }}


            {{-- https://stackoverflow.com/questions/35332784/how-to-call-a-controller-function-inside-a-view-in-laravel-5 --}}
            

            <div>
                <div>
                    {{-- botões --}}
                    {{-- Inputs --}}
                    {{-- {{ Html::ul($errors->get('InvolvedUsers'), ['class' => 'ulError']) }} --}}
                    <div id="repart2">
                        <div>
                            <span>Nome</span>
                            {{-- {{ Html::ul($errors->get('Requisitante'), ['class' => 'ulError']) }} --}}
                            <div class="form-field form-field-little" id="req"> <span></span>
                                {{-- nome input value if old is not null and if is null --}}
                                <input type="text" value="{{ old('nome') ?? $aluno->nome }}" name="nome" id="usuario">
                            </div>
                            <div>
                                <span>Tem acesso ao sistema?</span>
                                <div class="form-field form-field-littlePlus" id="ac"> <span></span>
                                    {{-- acesso select default value if old is not null and if is null --}}
                                    <select name="acesso" id="acesso">
                                        <option value="1" {{ (old('acesso') ?? $aluno->nivel_de_acesso) == 1 ? 'selected' : '' }}>
                                            Sim</option>
                                        <option value="0" {{ (old('acesso') ?? $aluno->nivel_de_acesso) == 0 ? 'selected' : '' }}>
                                            Não</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <span>Esta ativo?</span>
                                <div class="form-field form-field-littlePlus" id="at"> <span></span>
                                    {{-- acesso select default value if old is not null and if is null --}}
                                    <select name="ativo" id="ativo">
                                        <option value="1" {{ (old('ativo') ?? $aluno->ativo) == 1 ? 'selected' : '' }}>
                                            Sim</option>
                                        <option value="0" {{ (old('ativo') ?? $aluno->ativo) == 0 ? 'selected' : '' }}>
                                            Não</option>
                                    </select>
                                    
                                </div>
                            </div>
                            
                            <span>Curso</span>
                            {{-- {{ Html::ul($errors->get('Requisitante'), ['class' => 'ulError']) }} --}}
                            <div class="form-field form-field-little" id="req"> <span></span>
                                {{-- nome input value if old is not null and if is null --}}
                                <input type="text" value="{{ old('curso') ?? $aluno->curso }}" name="curso" id="curso">
                            </div>
                            @include('autocomplete', ['campo' => '#curso'])
                            
                            <div>
                                <span>Horario</span>
                                <div class="form-field form-field-littlePlus" id="at"> <span></span>
                                    {{-- horario select default value in case of old is not null and in case it is null --}}
                                    <select name="horario" id="horario">
                                        <option value="1" {{ (old('horario') ?? $aluno->horario) == 1 ? 'selected' : '' }}>
                                            Manhã</option>
                                        <option value="2" {{ (old('horario') ?? $aluno->horario) == 2 ? 'selected' : '' }}>
                                            Tarde</option>
                                        <option value="3" {{ (old('horario') ?? $aluno->horario) == 3 ? 'selected' : '' }}>
                                            Noite</option>
                                    </select>
                                </div>
                            </div>
                            
                            
    
                        </div>
                        <div id="userContainer">
                            Email
                            <div class="form-field form-field-little" id="req"> <span></span>
                                {{-- email input value if old is not null and if is null --}}
                                <input type="text" value="{{ old('email') ?? $aluno->email }}" name="email" id="email" required>
                            </div>
                            Telefone
                            <div class="form-field form-field-little" id="req"> <span></span>
                                {{-- telefone input value if old is not null and if is null --}}
                                <input type="tel" value="{{ old('telefone') ?? $aluno->telefone }}" name="telefone" id="telefone" required pattern="[0-9]{2} [0-9]{9}" placeholder="43 123456789">
                            </div>
    
                        </div>
    
    
                    </div>
    
                </div>
            </div>
           

            <div class="atvFormBtn">

                <div>

                    <a onclick="return confirm('Cancelar edição?')" href="{{ url('alunos/' . $aluno->aluno_id) }}">
                        <button type="button" class="btn dt-button outline-btn"
                            style="margin-top: 45px;">Cancelar</button>
                    </a>
                </div>
                <div class="btn-right">

                    {{ Form::submit('Salvar', ['class' => 'btn mt-3 dt-button', 'style' => 'margin-top: 45px',  'onclick'=>"return confirm('Confirmar edição?');"]) }}

                </div>
            </div>
            {{ Form::close() }}
        </div>




    </div>
</body>
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
</html>>
{{-- https://stackoverflow.com/questions/44517785/php-laravel-html-forms-array-of-values --}}
