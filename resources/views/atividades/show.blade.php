<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Atividade ID-{{$atv[0]->atividade_id}} - TutorLister</title>
    
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }

    </style>
</head>

{{-- clicar-carregar --}}
<script>
    function getClicked(id) {
        console.log(id)
    }
</script>


@include('sidemenu')

<body class="antialiased" id="eventBody">

    </div>
    <div class="wrapAll">
        
        <h1 >
            Atividade ID-{{$atv[0]->atividade_id}}
        </h1>


        {{-- detalhes do selecionado --}}
        <div class="AtvBtns">

            <a href={{ url('atividades') }}>
                <button class="miniBtn">Voltar</button>
            </a>
            <a href={{ url('atividades/' . $atv[0]->atividade_id .'/edit') }}>
                @if ($atv[0]->status == 'Arquivado')
                    <button class="miniBtn" disabled>Editar</button>
                @else
                    <button class="miniBtn">Editar</button>
                @endif
            </a>
            
        </div>
        
        <div class="atvDetalhes" style="margin-top: 0">
            <form class="p-3 mt-3 atvForm">
                <div>

                    Descrição
                    <div class="form-field formDesc"> <span></span>
                        <textarea name="descricao" id="descricao" cols="30" rows="10" class="desc" readonly>{{$atv[0]->descricao}}</textarea>
                    </div>
                </div>

                <div>
                    <div style="display: flex;">
                        
                        Usuarios envolvidos 
                    </div>

                    @foreach ($atv[0]->nome as $key => $value)
                    
                    <div id="userContainer">

                        <div class="form-field" id="involv"> <span></span>
                            
                            <input type="text" name="InvolvedUsers" id="InvolvedUsers" list="users" readonly value='{{$value}}'>
                            <datalist id="users">
                                <option value="(puxar de acordo com usuarios existentes no banco)"></option>
                            </datalist>
                        </div>
                    </div>
                    @endforeach
                    <span>Requisitante</span>
                    <div class="form-field" id="req"> <span></span>
                        
                        <input type="text" name="Requisitante" id="Requisitante" list="reqs" readonly value={{$atv[0]->requisitante->nome}}>
                        <datalist id="reqs">
                            <option value="(puxar de acordo com dados existentes no banco)"></option>
                        </datalist>
                    </div>
                    <div id="repart2">
                        <span>Organização do requisitante</span>
                        <span>Contato do requisitante</span>
                        
                        <div class="form-field" id="reqOrg"> <span></span>
                            <input type="text" name="orgRequisitante" id="orgRequisitante" list="orgReqs" readonly value={{$atv[0]->requisitante->empresa}}>
                            <datalist id="orgReqs">
                                <option value="(puxar de acordo com dados existentes no banco)"></option>
                            </datalist>
                        </div>
                        
                        
                        
                        <div class="form-field" id="req"> <span></span>
                            <input type="text" name="Requisitante" id="Requisitante" list="reqs" readonly value={{implode(" / ",[$atv[0]->requisitante->telefone,$atv[0]->requisitante->email])}}>
                            <datalist id="reqs">
                                <option value="(puxar de acordo com dados existentes no banco)"></option>
                            </datalist>
                        </div>
                    </div>

                    <div id="repart3">
                        <span>Data da atividade</span>
                        <span>Hora da atividade</span>
                        <span>Carga Horária</span>
                        <div class="form-field dth"> <span></span>
                            <input type="date" name="DoneData" id="DoneData" readonly value={{$atv[0]->data_atividade}}>
                        </div>
                        <div class="form-field dth"> <span></span>
                            <input type="time" name="DoneHour" id="DoneHour" readonly value={{$atv[0]->hora_atividade}}>
                        </div>
                        <div class="form-field" id="ch"> <span></span>
                            <input type="time" name="CargaHoraria" id="CargaHoraria" readonly value={{$atv[0]->carga}}>
                        </div> 
                        <div>
                            Data de registro
                            <div class="form-field dth"> <span></span>
                                <input type="date" name="regData" id="regData" readonly value={{$atv[0]->data_registro}}>
                            </div>
                        </div>
                        <div>
                            Hora de registro
                            <div class="form-field dth"> <span></span>
                                <input type="time" name="regHour" id="regHour" readonly value={{$atv[0]->hora_registro}}>
                            </div>
                        </div>
                        <div></div>
                        <div>
                            <span>Status</span>
                            <div class="form-field" id="ch"> <span></span>
                                <input type="text" name="status" id="status" readonly value="{{$atv[0]->status}}">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

</body>

</html>
