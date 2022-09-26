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

        
        
        <button class="btn mt-3"
        style="background-color: white; color: black; border-radius: 5px; font-size: 1.3em; display:grid; grid-template-columns: 20% 75%; gap: 5%; height: 3.5rem; border-color: #555"
        onclick="location.href='{{ url('/auth/redirect') }}'">
        <img style="margin-left: 20px; margin-top: 0.55rem;" src="{{ url('/image/google.png') }}" alt="">
        <div style="margin-right: 20px; margin-top: 0.55em;">Login com google</div>
    </button>
    {{-- if error exists show it --}}
    @if (session('error'))
        <div style="color: red; text-align: center; margin-top: 10px;" class="alert alert-success">
            {{ session('error') }}
        </div>
    @endif


    </div>
</body>

</html>
