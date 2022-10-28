<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Requisitante ID-{{$requisitante->requisitante_id}} - TutorLister</title>
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
        
        <h1>Requisitante: {{ $requisitante->nome }}</h1>
        <h3>ID-{{$requisitante->requisitante_id}}</h3>


        {{-- detalhes do selecionado --}}
        <div class="AtvBtns">

            <a style="text-decoration: none;" href={{ url('requisitantes') }}>
                <button class="dt-button">Voltar</button>
            </a>
            
            <a style="text-decoration: none;" href={{ url('requisitantes/' . $requisitante->requisitante_id .'/edit') }}>
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
                                <input readonly type="text" value="{{ $requisitante->nome }}" name="nome"  id="usuario">
                            </div>
                            <div>
                                <span>Empresa</span>
                                <div class="form-field form-field-littlePlus" id="ac"> <span></span>
                                    
                                    {{-- input version of the select above --}}
                                    <input readonly type="text" value="{{ $requisitante->empresa }}" name="empresa"  id="empresa">

                                </div>
                            </div>
                            
                        </div>
                        <div id="userContainer">
                            E-mail
                            <div class="form-field form-field-little" id="req"> <span></span>
                                <input readonly type="email" value="{{ $requisitante->email }}" name="email"  id="email">
                            </div>
                            Telefone
                            <div class="form-field form-field-little" id="req"> <span></span>
                                <input readonly type="tel" value="{{ $requisitante->telefone }}" name="telefone"  id="telefone" pattern="[0-9]{2} [0-9]{9}" placeholder="43 123456789">
                            </div>
    
                        </div>
    
    
                    </div>
    
                </div>
                
    
                
            </form>
        </div>
        @include('history')
    </div>
    @include('footer')

</body>

</html>
