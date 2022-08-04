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
    function clicarCarregar() {
        var x = document.getElementById("hist");
        if (x.style.display === "none") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
        }
    }
</script>

<div class="atvDetalhes histContainer">
    <h3 onclick="clicarCarregar()" class="itemTittle">Historico</h3>
    <div id="hist">
        test <br>
        test2
    </div>
</div>



</html>
