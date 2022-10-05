<?php

namespace App\Http\Controllers;
use Request;
use App\Models\horario;
use App\Models\usuario;
use Illuminate\Support\Facades\DB;
//use user logged data
use App\Http\Controllers\UserLoggedData;

class sistemaPontoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('sistemaPonto.index');
    }

    public function getData(){
        //retrieve all horario for the logged user DB::raw("DATE_FORMAT(atividade.data_registro, '%d/%m/%Y') as data_registro"),
        $user = usuario::find(auth()->user()->usuario_id);
        $horario = horario::select(
            DB::raw("DATE_FORMAT(horario.dia, '%d/%m/%Y') as dia"),
            DB::raw("DATE_FORMAT(horario.hora_inicio, '%H:%i') as hora_inicio"),
            DB::raw("DATE_FORMAT(horario.hora_fim, '%H:%i') as hora_fim"))
            ->where('usuario_id', $user->usuario_id)->get();
        
        return datatables()->of($horario)->toJson();
    }

    public function getData2(){
        //do the same as getData() but with all users and select name
        $horario = horario::select(
            DB::raw("DATE_FORMAT(horario.dia, '%d/%m/%Y') as dia"),
            DB::raw("DATE_FORMAT(horario.hora_inicio, '%H:%i') as hora_inicio"),
            DB::raw("DATE_FORMAT(horario.hora_fim, '%H:%i') as hora_fim"),
            'usuario.nome')
            ->join('usuario', 'usuario.usuario_id', '=', 'horario.usuario_id')->get();

        return datatables()->of($horario)->toJson();

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd(Request::all());
        
        //if the newest horario in DB from the user is a null hora_fim, then edit its hora fim to the Request hora, else create a new horario
        $user = usuario::find(auth()->user()->usuario_id);
        $horario = horario::where('usuario_id', $user->usuario_id)->orderBy('horario_id', 'desc')->first();
        if($horario->hora_fim == null and  $horario->dia == date('Y-m-d')){
            $horario->hora_fim = Request::input('hora');
            $horario->save();
        }else{
            $horario = new horario();
            $horario->dia = Request::input('dia');
            $horario->hora_inicio = Request::input('hora');
            $horario->usuario_id = $user->usuario_id;
            $horario->save();
        }
        //redirect to same url
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
