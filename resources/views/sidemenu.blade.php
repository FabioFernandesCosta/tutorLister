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
          <a href={{ url('dashboard') }}>
            <img src="{{url('/image/activity.png')}}" />
            <span>Dashboard</span></a>
        </li>
        <li>
          <a href={{ url('atividades') }}>
            <img src="{{url('/image/notes.png')}}" />
            <span>Atividade</span></a>
        </li>
        <li>
          <a href={{ url('alunos') }}>
            <img src="{{url('/image/user.png')}}" />
            <span>Alunos</span></a>
        </li>
        <li>
          <a href={{ url('cursos') }}>
            <img src="{{url('/image/curso.png')}}" />
            <span>Cursos</span></a>
        </li>
        <li>
          <a href={{ url('requisitantes') }}>
            <img src="{{url('/image/grupo.png')}}" />
            <span>requisitantes</span></a>
        </li>
        <li>
          <a id="usuarioContainer" href="javascript:void(0);">
            
            {{-- img src null --}}
            <img id="userAvatar" style="border-radius: 50%;" src="{{url('/image/user.png')}}" onerror="this.src='{{url('/image/user.png')}}'" />
            <span id="userName" >usuario</span></a>

          </li>
        </ul>
      </nav>
      <div id="menuHide">
        <div style="margin: auto" id="fullName">usuario</div>
        <div style="margin-top: 10px">
          <a href="" style="margin: auto;" id="editUser">
            <button style="; margin: auto; font-size: 0.9rem;" class="dt-button" >Editar seus dados</button>
          </a>
        </div>
        <div>
          <button style="margin-top: 10px;" id="logoutBtn" class="dt-button">Logout</button>
        </div>
      </div>
    {{-- hide/show menu with name and logout button --}}
    
</header>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-1.12.4.js"></script>
<link rel="stylesheet" type="text/css"
                href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
<script>
  //ajax to retrieve data from route userLoggedData
  var id;
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

        //retrieve full name from string data.name
        $('#fullName').text(data.name);
        //retrieve id from data.id and put as href in editUser
        $('#editUser').attr('href', "{{ url('alunos') }}"+"/"+data.id+"/edit");
      }
    });
    // transition: visibility 0.25s, opacity 0.25s linear, left 0.25s linear;
    $('#menuHide').css('transition', 'visibility 0.25s, opacity 0.25s linear, left 0.25s linear');


    $('.mainHead').hover(function(){
          $('#menuHide').css('left', '14rem');
        }, function(){
          $('#menuHide').css('left', '4.5rem');
        });
    //when usuarioContainer is clicked, show menuHide or hide menuHide
    $('#usuarioContainer').click(function(){
      
      if($('#menuHide').css('visibility') == 'hidden'){
        //position menuHide as absolute to the right of usuarioContainer

        $('#menuHide').css('visibility', 'visible');
        $('#menuHide').css('opacity', '1');
      }else{
        $('#menuHide').css('visibility', 'hidden');
        $('#menuHide').css('opacity', '0');
      }
    });

    //click anywere outside of menuHide or usuarioContainer to hide menuHide or both of those are not hovered
    $(document).click(function(event){
      if(!$(event.target).closest('#usuarioContainer').length && !$(event.target).closest('#menuHide').length){
        $('#menuHide').css('visibility', 'hidden');
        $('#menuHide').css('opacity', '0');
      }
    });

    //when mainhead not hovered, hide menuHide
    $('.mainHead').mouseleave(function(){
      $('#menuHide').css('visibility', 'hidden');
      $('#menuHide').css('opacity', '0');
    });

    //when logoutBtn is clicked, redirect to logout route
    $('#logoutBtn').click(function(){
      window.location.href = "{{ url('logout') }}";
    });





  });

  
</script>

</html>
