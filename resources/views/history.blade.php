<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">


    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
    </style>
</head>
<script>
    var scr = false;

    function clicarCarregar() {
        var x = document.getElementById("JqueryAtvHistTableCont");
        var y = document.getElementById("histClick");
        var z = document.getElementById("histContainer");
        var a = document.getElementById("tbr").offsetHeight;


        if (x.style.visibility === "hidden") {
            z.style.transition = "height 0.25s linear";
            x.style.transition = "visibility 0.25s 0.25s, opacity 0.25s 0.25s linear";
            x.style.visibility = "visible";
            x.style.opacity = "1";
            y.innerHTML = "Historico ▲";
            size = (document.getElementById("JqueryAtvHistTable").rows.length * a) + 2.3 * a;
            z.style.height = size + "px";


        } else {

            z.style.transition = "height 0.25s 0.25s linear";
            x.style.transition = "visibility 0.25s, opacity 0.25s linear";
            x.style.visibility = "hidden";
            x.style.opacity = "0";
            y.innerHTML = "Historico ▼";
            z.style.height = "5.5rem";
        }
    }
</script>

<div id="histContainer" style="margin-top: 0.8rem" class="atvDetalhes histContainer">
    <h3 onclick="clicarCarregar()" id="histClick" class="itemTittle" style="cursor:pointer">Historico ▼</h3>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.dataTables.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/datetime/1.1.2/css/dataTables.dateTime.min.css">

    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.js"></script>




    <div>

    </div>


    <div id="JqueryAtvHistTableCont">

        <table id="JqueryAtvHistTable" class="display nowrap dataTable " style="width:100%">
            <thead>
                <tr id="tbr">
                    <th>ID</th>
                    <th>Autor</th>
                    <th>Ação</th>
                    <th>Campo modificado</th>
                    <th>Valor anterior</th>
                    <th>Novo valor</th>
                    <th>Data</th>
                </tr>
            </thead>
        </table>
    </div>

</div>

<script>
    let atvId;

    if (window.location.href.indexOf("atividades") > -1) {
        atvId = '<?php try{echo $atv[0]->atividade_id;}catch(Exception $e){} ?>' + '/historico';
    }
    else if(window.location.href.indexOf("alunos") > -1) {
        atvId = '<?php try{echo $aluno->usuario_id;}catch(Exception $e){} ?>' + '/historicoUser';
    }
    console.log(atvId);
    $(document).ready(function() {
        console.log(atvId);
        $('#JqueryAtvHistTable').DataTable({
            "ordering": false,
            searching: false,
            paging: false,
            info: false,
            "processing": true,
            "serverSide": true,
            scrollX: true,
            "ajax": atvId,
            "columns": [{
                    "data": "historico_id"
                },
                {
                    "data": "nome",
                },
                {
                    "data": "acao",
                },
                {
                    "data": "campo_modificado",
                },
                {
                    "data": "valor_anterior",
                },
                {
                    "data": "novo_valor",
                },
                {
                    "data": "data_modificacao",
                }
            ]
        });
    });
</script>
<script defer>
    var x = document.getElementById("JqueryAtvHistTableCont");
    var y = document.getElementById("histClick");
    var z = document.getElementById("histContainer");
    var a = document.getElementById("tbr").offsetHeight;

    x.style.visibility = "hidden";
    x.style.opacity = "0";
    y.innerHTML = "Histórico ▼";
    z.style.height = "5.5rem";
</script>

</html>
