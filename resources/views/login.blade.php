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
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
</head>

<body class="antialiased">
    <div class="wrapper">
        <div class="text-center mt-4 name"> Tutor Lister</div>

        {{-- normal login fields --}}
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">


                        <div class="card-body">
                            <form method="POST" action="{{ route('default.login') }}">
                                @csrf

                                <div class="form-group
                                    row">
                                    <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail') }}</label>
                                    
                                    <div class="form-field form-field-little">
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                        
                                        
                                    </div>
                                </div>
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    {{-- {{ Html::ul($errors->get('InvolvedUsers'), ['class' => 'ulError']) }} --}}
                                    {{-- <strong >{{ $message }}</strong> --}}
                                    <span class="ulError">{{$message}}</span><br>
                                </span>
                                @enderror

                                <div class="form-group
                                    row">
                                    <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Senha') }}</label>

                                    <div class="form-field form-field-little">
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                    </div>
                                </div>
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror

                                <div class="form-group
                                    row mb-0">
                                    <div class="col-md-8 offset-md-4">
                                        <button type="submit" class="btn mt-3 dt-button">
                                            {{ __('Login') }}
                                        </button>

                                        @if (Route::has('password.request'))
                                        <a class="btn btn-link" href="{{ route('password.request') }}">
                                            {{ __('Forgot Your Password?') }}
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
                                    
        
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
