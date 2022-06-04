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
          <a href={{ url('atividades') }}>
            <span>Atividade</span></a>
        </li>
        <li>
          <a href={{ url('dashboard') }}>
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
            <span>user</span></a>
        </li>
      </ul>
    </nav>
</header>

</html>
