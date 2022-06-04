<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">


    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }

    </style>
</head>

<body class="antialiased">
    <div class="wrapper">
        <div class="text-center mt-4 name"> Tutor Lister</div>
        <form class="p-3 mt-3">
            <div class="form-field align-items-center"> <span class="far fa-user"></span> <input type="text"
                    name="userName" id="userName" placeholder="Username">
            </div>
            <div class="form-field align-items-center"> <span class="fas fa-key"></span> <input type="password"
                    name="password" id="pwd" placeholder="Password">
            </div>
            <div class="cSelect align-items-center"> <span class=""></span> <select id="org" name="orgList">
                    <option selected disabled>Escolha a organização</option>
                    <option value=1>Aluno tutor</option>
                    <option value=2>NPI</option>
                </select>
            </div>

        </form>
        <button class="btn mt-3" onclick="location.href='{{ url('dashboard') }}'">Login</button>
        <div class="text-center fs-6"> <a href="#">Esqueceu a senha?</a>
        </div>
</body>

</html>
