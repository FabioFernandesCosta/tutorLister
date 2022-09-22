<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\atividadeControler;
use App\Http\Controllers\historicoController;
use App\Http\Controllers\alunosController;
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

Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/', function () {
    return view('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/atividades/import', function () {
    return view('atividades.import');
});

/*

Route::get('/eventos', function () {
    return view('eventos');
});
*/
//alunos
Route::get('alunos/getdata', [alunosController::class, 'getdata']);
Route::controller(alunosController::class)->group(function(){
    Route::resource('alunos', alunosController::class);
    
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



/*
Modelo para obter exibição de tabela:           https://stackoverflow.com/questions/43090063/how-to-get-data-from-database-to-view-page-in-laravel
Usar controlers por questões de vulnerabilide:  https://laravel.com/docs/8.x/controllers
Prep banco de dados                             https://imasters.com.br/data/utilizando-docker-com-mysql
database seeding para popular banco
models                                          https://imasters.com.br/back-end/como-criar-as-models-do-seu-projeto-com-eloquent-no-laravel
controllers                                     https://laravel.com/docs/9.x/controllers
*/