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

<header class="mainHead">
    <nav class="headNav">
      <ul class="menu">
        <li>
          <a>
            <img id="menuIcon" src="{{url('/image/Menu.png')}}" />
            <span style="font-size: 1.6rem; margin-top: 1rem;">TutorLister</span>
          </a>
        </li>
        <li>
          <a href={{ url('atividades') }}>
            <img src="{{url('/image/notes.png')}}" />
            <span>Atividade</span></a>
        </li>
        <li>
          <a href={{ url('dashboard') }}>
            <img src="{{url('/image/activity.png')}}" />
            <span>Dashboard</span></a>
        </li>
        <li>
          <a href="">
            <span>m3</span></a>
        </li>
        <li>
          <a href="">
            <span>m4</span></a>
        </li>
        <li>
          <a href="">
            <img src="{{url('/image/user.png')}}" />
            <span>user</span></a>
        </li>
      </ul>
    </nav>
</header>

</html>
