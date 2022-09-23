<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\atividadeControler;
use App\Http\Controllers\historicoController;
use App\Http\Controllers\alunosController;
use Laravel\Socialite\Facades\Socialite;
use App\Models\usuario;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    return view('login');
});




//rotas google OAuth solialite
Route::get('/auth/redirect', function () {
    return Socialite::driver('google')->redirect();
});


Route::get('login/google/callback', function () {
    //dd(var_dump(openssl_get_cert_locations()));
    $googleUser = Socialite::driver('google')->stateless()->user();

    // check if email already exists and nive_de_acesso = 1
    $user = usuario::where('email', $googleUser->email)->first();

    if ($user) {
        if ($user->nivel_de_acesso == 1) {
            //dd('test');
            //save avatar
            $user->avatar = $googleUser->avatar;
            $user->save();
            //dd($user->avatar);
            //login
            Auth::login($user);
            return redirect('/dashboard');
        } else {
            return redirect('/')->with('error', 'Você não tem permissão para acessar o sistema');
        }

    } else {
        //return to login with error message "usuario não registrado".
        return redirect('/') -> with('error', 'usuario não registrado');
    }  
});
        
Route::group( ['middleware' => 'auth' ], function() {

    Route::get('/dashboard', function () {
        return view('dashboard');
    });
    
    Route::get('/atividades/import', function () {
        return view('atividades.import');
    });
    
    Route::get('/alunos/import', function () {
        return view('alunos.import');
    });
            
    //route to getData from UserLoggedData controller
    Route::get('/userLoggedData', [App\Http\Controllers\UserLoggedData::class, 'getData']);
    
    
    
    
    /*
    
    Route::get('/eventos', function () {
        return view('eventos');
    });
    */
    //alunos
    Route::get('alunos/getdata', [alunosController::class, 'getdata']);
    Route::controller(alunosController::class)->group(function(){
        Route::resource('alunos', alunosController::class);
        Route::post('/alunos/import/store', 'import_alunos')->name('alunos.import_alunos');
        Route::post('/alunos/getdata', 'getdata')->name('alunos.getData');
    });
    
    
    
    //Route::resource('requisitante', RequisitanteController::class);
    
    Route::get('consultar', [atividadeControler::class, 'consultar']);
    Route::get('atividades/getdata', [atividadeControler::class, 'getdata']);
    
    
    Route::controller(atividadeControler::class)->group(function(){
        Route::resource('atividades', atividadeControler::class);
        Route::post('atv-export/', 'export')->name('atividade.export');
        //Route::get('atividades/index-filtering', 'atividadeControler@indexFiltering');
        Route::post('/atividades/import/store', 'import_atv')->name('atividade.import_atv');
        Route::post('atividades/getdata/', 'getdata')->name('atividade.getdata');
        
    });
    
    //historico
    Route::get('atividades/{id}/historico', [historicoController::class, 'show']);
    //historicoUser
    Route::get('alunos/{id}/historicoUser', [historicoController::class, 'showUser']);
});




