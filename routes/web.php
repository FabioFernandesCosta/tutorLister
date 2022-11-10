<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\atividadeControler;
use App\Http\Controllers\historicoController;
use App\Http\Controllers\alunosController;
use App\Http\Controllers\cursoController;
use App\Http\Controllers\requisitanteController;
use App\Http\Controllers\sistemaPontoController;
use App\Http\Controllers\DashboardController;
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
})->name('login');




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
        if ($user->nivel_de_acesso >= 1) {
            $user->avatar = $googleUser->avatar;
            $user->save();
            Auth::login($user);
            return redirect('/dashboard');

        } else {
            return redirect('/')->with('error', 'Você não tem permissão para acessar o sistema');
        }

    } else {
        //return to login with error message "usuario não registrado".
        return redirect('/') -> with('error', 'Email não registrado no sistema');
    }  
});

//normal login in alunoscontroler
Route::post('/login/default', [alunosController::class, 'login'])->name('default.login');


        
Route::group( ['middleware' => 'auth' ], function() {


    


    //logout
    Route::get('/logout', function () {
        Auth::logout();
        return redirect('/');
    })->name('logout');

    // Route::get('/charts', 'ChartController@index')->name('charts');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::controller(DashboardController::class)->group(function(){
        //post version
        Route::post('/dashboard', [DashboardController::class, 'index'])->name('dashboard.post');
    });
    


    //any import view is protected by admin middleware
    Route::group( ['middleware' => 'isAdmin' ], function() {
        Route::get('/atividades/import', function () {
            return view('atividades.import');
        });
        
        Route::get('/alunos/import', function () {
            return view('alunos.import');
        });
        
        Route::get('/cursos/import', function () {
            return view('cursos.import');
        });
    });
    
    

            
    //route to getData from UserLoggedData controller
    Route::get('/userLoggedData', [App\Http\Controllers\UserLoggedData::class, 'getData']);
    
    
    
    
    /*
    
    Route::get('/eventos', function () {
        return view('eventos');
    });
    */
    //alunos
    Route::group( ['middleware' => 'isAdmin' ], function() {

        Route::get('alunos/getdata', [alunosController::class, 'getdata']);
        Route::controller(alunosController::class)->group(function(){
            Route::resource('alunos', alunosController::class)->except(['destroy']);
            Route::post('/alunos/import/store', 'import_alunos')->name('alunos.import_alunos');
            Route::post('/alunos/getdata', 'getdata')->name('alunos.getData');
        });
        //historicoUser
        
        
        Route::get('cursos/getdata', [cursoController::class, 'getdata']);
        Route::controller(cursoController::class)->group(function(){
            //route resource cursos without destroy and show
            Route::resource('cursos', cursoController::class, ['except' => ['destroy']]);
            Route::post('/cursos/import/store', 'import_cursos')->name('cursos.import_cursos');
            Route::post('/cursos/getdata', 'getdata')->name('cursos.getData');
        });
        Route::get('cursos/{id}/historicoCurso', [historicoController::class, 'showCurso']);
        
        
    }); //admin middleware
    //route to alunos edit, update and show
    Route::get('alunos/{id}/historicoUser', [historicoController::class, 'showUser']);
    Route::get('alunos/{id}/atividades', [alunosController::class, 'atvsUser']);
    Route::get('alunos/{id}/selfEdit', [alunosController::class, 'edit']);
    Route::post('alunos/{id}/selfUpdate', [alunosController::class, 'updateSelf'])->name('alunos.selfUpdate');
    Route::get('alunos/{id}/selfShow', [alunosController::class, 'show'])->name('alunos.selfShow');





    
    Route::get('consultar', [atividadeControler::class, 'consultar']);
    Route::get('atividades/getdata', [atividadeControler::class, 'getdata']);
    

    Route::controller(atividadeControler::class)->group(function(){
        Route::resource('atividades', atividadeControler::class)->except(['destroy']);
        Route::post('atv-export/', 'export')->name('atividade.export');
        //Route::get('atividades/index-filtering', 'atividadeControler@indexFiltering');
        Route::post('/atividades/import/store', 'import_atv')->name('atividade.import_atv');
        Route::post('atividades/getdata/', 'getdata')->name('atividade.getdata');
        
    });
    
    //historico
    Route::get('atividades/{id}/historico', [historicoController::class, 'show']);
    


    

    Route::get('requisitantes/getdata', [requisitanteController::class, 'getdata']);
    Route::get('requisitantes/consultar', [requisitanteController::class, 'consultar']);
    Route::controller(requisitanteController::class)->group(function(){
        Route::resource('requisitantes', requisitanteController::class, ['except' => ['destroy']]);
        Route::post('/requisitantes/getdata', 'getdata')->name('requisitantes.getData');
    });
    Route::get('requisitantes/{id}/historicoRequisitantes', [historicoController::class, 'showRequisitante']);

    Route::get('ponto/getdata', [sistemaPontoController::class, 'getdata']);
    Route::get('ponto/getdataAll', [sistemaPontoController::class, 'getdata2']);
    Route::controller(sistemaPontoController::class)->group(function(){
        // the route above, but only with index and store
        Route::resource('ponto', sistemaPontoController::class, ['only' => ['index', 'store']]);
        Route::post('/ponto/getdata', 'getdata')->name('sistemaPontoController.getData');
        //same as above but for getdata2
        Route::post('/ponto/getdataAll', 'getdata2')->name('sistemaPontoController.getData2');
    });


});

Route::fallback(function () {
    abort(404);
});



