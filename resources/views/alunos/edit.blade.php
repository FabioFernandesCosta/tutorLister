<!DOCTYPE html>
<html>

<head>
    <title>Aluno ID-{{ $aluno->usuario_id }} - TutorLister</title>
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
</head>
@include('sidemenu')

<body class="antialiased" id="eventBody">
    <div class="wrapAll">


        <h1>Editar Aluno: {{ $aluno->nome }}</h1>
        <h3>ID-{{ $aluno->usuario_id }}</h3>

        {{-- if theres any error show it --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div><br />
        @endif

        {{-- if theres any success show it --}}

        <div class="atvDetalhes" style="height: auto">

            @if (Auth::user()->usuario_id == $aluno->usuario_id)
                {{ Form::model($aluno, ['route' => ['alunos.selfUpdate', $aluno->usuario_id], 'method' => 'POST', 'class' => 'atvForm', 'autocomplete' => 'off']) }}
            @else
                {{ Form::model($aluno, ['route' => ['alunos.update', $aluno->usuario_id], 'method' => 'PUT', 'class' => 'atvForm', 'autocomplete' => 'off']) }}
            @endif


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
                                <input type="text" value="{{ old('nome') ?? $aluno->nome }}" name="nome"
                                    id="usuario">
                            </div>
                            @if (Auth::user()->admin == 1)
                                <div>
                                    <span>Níveis de acesso ao sistema</span>
                                    {{ Html::ul($errors->get('acesso'), ['class' => 'ulError']) }}
                                    <div class="form-field form-field-littlePlus" id="ac"> <span></span>
                                        {{-- acesso select default value if old is not null and if is null --}}

                                        <select name="acesso" id="acesso">
                                            <option value="1"
                                                {{ (old('acesso') ?? $aluno->nivel_de_acesso) == 'NPI' ? 'selected' : '' }}>
                                                NPI</option>
                                            <option value="2"
                                                {{ (old('acesso') ?? $aluno->nivel_de_acesso) == 'Aluno Tutor' ? 'selected' : '' }}>
                                                Aluno Tutor</option>
                                            <option value="3"
                                                {{ (old('acesso') ?? $aluno->nivel_de_acesso) == 'NPI e Aluno Tutor' ? 'selected' : '' }}>
                                                NPI e Aluno Tutor</option>
                                            <option value="0"
                                                {{ (old('acesso') ?? $aluno->nivel_de_acesso) == 'Não' ? 'selected' : '' }}>
                                                Não</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- é admin --}}
                                <div>
                                    <span>É admin?</span>
                                    {{ Html::ul($errors->get('admin'), ['class' => 'ulError']) }}
                                    <div class="form-field form-field-littlePlus" id="ad"> <span></span>
                                        {{-- acesso select default value if old is not null and if is null --}}
                                        <select name="admin" id="admin">
                                            <option value="1"
                                                {{ (old('admin') ?? $aluno->admin) == 1 ? 'selected' : '' }}>
                                                Sim</option>
                                            <option value="0"
                                                {{ (old('admin') ?? $aluno->admin) == 0 ? 'selected' : '' }}>
                                                Não</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <span>Está no NPI?</span>
                                    {{ Html::ul($errors->get('npi'), ['class' => 'ulError']) }}
                                    <div class="form-field form-field-littlePlus" id="np"> <span></span>
                                        {{-- acesso select default value if old is not null and if is null --}}

                                        <select name="npi" id="npi">
                                            <option value="1"
                                                {{ (old('npi') ?? $aluno->npi) == 'Sim' ? 'selected' : '' }}>
                                                Sim</option>
                                            <option value="0"
                                                {{ (old('npi') ?? $aluno->npi) == 'Não' ? 'selected' : '' }}>
                                                Não</option>
                                        </select>

                                    </div>
                                </div>
                                <div>
                                    <span>Está no Aluno Tutor?</span>
                                    {{ Html::ul($errors->get('aluno_tutor'), ['class' => 'ulError']) }}
                                    <div class="form-field form-field-littlePlus" id="at"> <span></span>
                                        {{-- acesso select default value if old is not null and if is null --}}
                                        <select name="aluno_tutor" id="aluno_tutor">
                                            <option value="1"
                                                {{ (old('aluno_tutor') ?? $aluno->aluno_tutor) == 'Sim' ? 'selected' : '' }}>
                                                Sim</option>
                                            <option value="0"
                                                {{ (old('aluno_tutor') ?? $aluno->aluno_tutor) == 'Não' ? 'selected' : '' }}>
                                                Não</option>
                                        </select>

                                    </div>
                                </div>
                                {{-- treinamento_concluído --}}
                                <div>

                                    <span>Treinamento concluído?</span>
                                    {{ Html::ul($errors->get('treinamento_concluido'), ['class' => 'ulError']) }}
                                    <div class="form-field form-field-littlePlus" id="at"> <span></span>
                                        <select name="treinamento_concluido" id="treinamento_concluido">
                                            <option value="1"
                                                {{ (old('treinamento_concluido') ?? $aluno->treinamento_concluido) == 'NPI' ? 'selected' : '' }}>
                                                NPI</option>
                                            <option value="2"
                                                {{ (old('treinamento_concluido') ?? $aluno->treinamento_concluido) == 'Aluno Tutor' ? 'selected' : '' }}>
                                                Aluno Tutor</option>
                                            <option value="3"
                                                {{ (old('treinamento_concluido') ?? $aluno->treinamento_concluido) == 'NPI e Aluno Tutor' ? 'selected' : '' }}>
                                                NPI e Aluno Tutor</option>
                                            <option value="0"
                                                {{ (old('treinamento_concluido') ?? $aluno->treinamento_concluido) == 'Nenhum' ? 'selected' : '' }}>
                                                Nenhum</option>
                                        </select>

                                    </div>
                                </div>

                                <span>Curso</span>
                                {{ Html::ul($errors->get('curso'), ['class' => 'ulError']) }}
                                <div class="form-field form-field-little" id="req"> <span></span>
                                    {{-- nome input value if old is not null and if is null --}}
                                    <input type="text" value="{{ old('curso') ?? $aluno->curso }}" name="curso"
                                        id="curso">
                                </div>
                                @include('autocomplete', ['campo' => '#curso'])

                                <div>
                                    <span>Horário</span>
                                    {{ Html::ul($errors->get('horario'), ['class' => 'ulError']) }}
                                    <div class="form-field form-field-littlePlus" id="at"> <span></span>
                                        {{-- horario select default value in case of old is not null and in case it is null --}}
                                        <select name="horario" id="horario">
                                            <option value="Manhã"
                                                {{ (old('horario') ?? $aluno->horario) == 'Manhã' ? 'selected' : '' }}>
                                                Manhã</option>
                                            <option value="Tarde"
                                                {{ (old('horario') ?? $aluno->horario) == 'Tarde' ? 'selected' : '' }}>
                                                Tarde</option>
                                            <option value="Noite"
                                                {{ (old('horario') ?? $aluno->horario) == 'Noite' ? 'selected' : '' }}>
                                                Noite</option>
                                        </select>
                                    </div>

                                </div>



                        </div>
                        @endif
                        <div id="userContainer">
                            E-mail
                            {{ Html::ul($errors->get('email'), ['class' => 'ulError']) }}
                            <div class="form-field form-field-little" id="req"> <span></span>
                                {{-- email input value if old is not null and if is null --}}
                                <input type="text" value="{{ old('email') ?? $aluno->email }}" name="email"
                                    id="email" required>
                            </div>
                            {{-- if url contains Auth::user()->usuario_id --}}
                            @if ($aluno->usuario_id == Auth::user()->usuario_id)
                                Senha
                                {{ Html::ul($errors->get('password'), ['class' => 'ulError']) }}
                                <div style="display: grid; grid-template-columns: 95% auto; gap: 1%">
                                    <div class="form-field form-field-little" id="req"> <span></span>
                                        {{-- password input value if old is not null and if is null --}}
                                        <input type="password" value="{{ old('password') ?? $aluno->password }}"
                                            name="password" id="password" required>
                                    </div><a href="javascript:void(0)" id="passwordImg" class="hidden"><img
                                            style="margin: auto; margin-top: 0"
                                            src="{{ url('/image/hidden.png') }}" /></a>
                                </div>
                            @endif
                            Telefone
                            {{ Html::ul($errors->get('telefone'), ['class' => 'ulError']) }}
                            <div class="form-field form-field-little" id="req"> <span></span>
                                {{-- telefone input value if old is not null and if is null --}}
                                <input type="tel" value="{{ old('telefone') ?? $aluno->telefone }}" name="telefone"
                                    id="telefone" required pattern="[0-9]{2} [0-9]{9}" placeholder="43 123456789">
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
        // when hidden is clicked change img to show and change type to text
        $("#passwordImg").click(function() {
            if ($("#password").attr("type") == "password") {
                $("#password").attr("type", "text");
                $("#passwordImg").html(
                    "<img style='margin: auto; margin-top: 0' src='{{ url('/image/show.png') }}' />"
                    );
            } else {
                $("#password").attr("type", "password");
                $("#passwordImg").html(
                    "<img style='margin: auto; margin-top: 0' src='{{ url('/image/hidden.png') }}' />"
                    );
            }
        });

    });
</script>

</html>
{{-- https://stackoverflow.com/questions/44517785/php-laravel-html-forms-array-of-values --}}
