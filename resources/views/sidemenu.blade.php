{{-- use auth --}}

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
          <a href={{ url('alunos') }}>
            <img src="{{url('/image/user.png')}}" />
            <span>Alunos</span></a>
        </li>
        <li>
          <a href="">
            <span>m4</span></a>
        </li>
        <li>
          <a href="">
            

            {{-- img src null --}}
            <img id="userAvatar" style="border-radius: 50%;" src="{{url('/image/user.png')}}" />






            <span id="userName" >user</span></a>
        </li>
      </ul>
    </nav>
</header>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script>
  //ajax to retrieve data from route userLoggedData
  $(document).ready(function(){
    $.ajax({
      url: "{{ url('userLoggedData') }}",
      type: "GET",
      dataType: "json",
      success: function(data){
        //retrieve column avatar and user name from route userLoggedData
        $('#userAvatar').attr('src', data.avatar);
        //retrieve first name from string data.name
        $('#userName').text(data.name.substr(0, data.name.indexOf(' ')));
      }
    });
  });

  
</script>

</html>
