<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Atividade ID-{{$aluno->uuario_id}} - TutorLister</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }

    </style>
</head>

{{-- clicar-carregar --}}



@include('sidemenu')

<body class="antialiased" id="eventBody">

    </div>
    <div class="wrapAll">
        
        <h1 >
            {{-- id aluno --}}
            Aluno ID-{{$aluno->usuario_id}}
        </h1>


        {{-- detalhes do selecionado --}}
        <div class="AtvBtns">

            <a style="text-decoration: none;" href={{ url('alunos') }}>
                <button class="dt-button">Voltar</button>
            </a>
            {{-- @php
                dd($aluno->usuario_id);
            @endphp --}}
            <a style="text-decoration: none;" href={{ url('alunos/' . $aluno->usuario_id .'/edit') }}>
                <button class="dt-button">Editar</button>
            </a>
            
        </div>
        
        
        
        <div class="atvDetalhes" style="margin-top: 0">
            <h3 class="itemTittle">Detalhes</h3>
            <form class="p-3 mt-3 atvForm">
                

                <div>
                    {{-- botões --}}
                    {{-- Inputs --}}
                    {{-- {{ Html::ul($errors->get('InvolvedUsers'), ['class' => 'ulError']) }} --}}
                    <div id="repart2">
                        <div>
                            <span>Nome</span>
                            {{-- {{ Html::ul($errors->get('Requisitante'), ['class' => 'ulError']) }} --}}
                            <div class="form-field form-field-little" id="req"> <span></span>
                                <input readonly type="text" value="{{ $aluno->nome }}" name="nome"  id="usuario">
                            </div>
                            <div>
                                <span>Tem acesso ao sistema?</span>
                                <div class="form-field form-field-littlePlus" id="ac"> <span></span>
                                    
                                    {{-- input version of the select above --}}
                                    <input readonly type="text" value="{{ $aluno->nivel_de_acesso }}" name="acesso"  id="acesso">

                                    
                                </div>
                            </div>
                            <div>
                                <span>Esta ativo?</span>
                                <div class="form-field form-field-littlePlus" id="at"> <span></span>
                                    
                                    {{-- input version of the select above --}}
                                    <input readonly type="text" value="{{ $aluno->ativo }}" name="ativo"  id="ativo">
                                </div>
                            </div>
    
                            <span>Curso</span>
                            {{-- {{ Html::ul($errors->get('Requisitante'), ['class' => 'ulError']) }} --}}
                            <div class="form-field form-field-little" id="req"> <span></span>
                                <input readonly type="text" value="{{ $aluno->curso }}" name="curso"  id="curso">
                            </div>

                            <span>Horario do curso</span>
                            {{-- {{ Html::ul($errors->get('Requisitante'), ['class' => 'ulError']) }} --}}
                            <div class="form-field form-field-little" id="req"> <span></span>
                                <input readonly type="text" value="{{ $aluno->horario }}" name="horario"  id="horario">
                            </div>
                            
                            
                            
    
                        </div>
                        <div id="userContainer">
                            Email
                            <div class="form-field form-field-little" id="req"> <span></span>
                                <input readonly type="email" value="{{ $aluno->email }}" name="email"  id="email">
                            </div>
                            Telefone
                            <div class="form-field form-field-little" id="req"> <span></span>
                                <input readonly type="tel" value="{{ $aluno->telefone }}" name="telefone"  id="telefone" pattern="[0-9]{2} [0-9]{9}" placeholder="43 123456789">
                            </div>
    
                        </div>
    
    
                    </div>
    
                </div>
                
    
                
            </form>
        </div>
        @include('history')
    </div>

</body>

</html>
