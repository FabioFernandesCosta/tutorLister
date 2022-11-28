<?php

namespace App\Http\Controllers;
use Request;
use App\Models\horario;
use App\Models\usuario;
use Illuminate\Support\Facades\DB;
use Redirect;
use App\Http\Controllers\UserLoggedData;
use Illuminate\Support\Facades\View;

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

    public function getData2(Request $request){
        //do the same as getData() but with all users and select name
        $horario = horario::select(
            DB::raw("DATE_FORMAT(horario.dia, '%d/%m/%Y') as dia"),
            DB::raw("DATE_FORMAT(horario.hora_inicio, '%H:%i') as hora_inicio"),
            DB::raw("DATE_FORMAT(horario.hora_fim, '%H:%i') as hora_fim"),
            'usuario.nome',
            'usuario.usuario_id')
            ->join('usuario', 'usuario.usuario_id', '=', 'horario.usuario_id');
        //if auth user npi = 1; where usuario.npi = 1, if auth user aluno_tutor = 1; where usuario.aluno_tutor = 1, if both = 1; all users
        


        $min = strtotime(Request::get("min"));
        $max = strtotime(Request::get("max"));
        
        if ($min != null && $max == null) {
            $min = date("Y-m-d", ($min) );
            $horario = $horario->whereRaw (("DATE(horario.dia) >= '".($min)."'"));
        }
        
        //if (min null and max not null) { where data_atividade <= max}
        elseif ($min == null && $max != null) {
            $max = date("Y-m-d", ($max) );
            $horario = $horario->whereRaw (("DATE(horario.dia) <= '".($max)."'"));
        }
        
        //if (min not null and max not null) { where data_atividade between min and max}
        elseif ($min != null && $max != null) {
            $min = date("Y-m-d", ($min) );
            $max = date("Y-m-d", ($max) );
            $horario = $horario->whereRaw(("DATE(horario.dia) between '".($min)."' and '".($max)."'"));
            
        }

        if(auth()->user()->npi == 1 && auth()->user()->aluno_tutor == 1){

        }else if(auth()->user()->npi == 1){
            $horario = $horario->where('usuario.npi', 1);
        }else if(auth()->user()->aluno_tutor == 1){
            $horario = $horario->where('usuario.aluno_tutor', 1);
        }
        

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
        
        //if the newest horario in DB from the user is a null hora_fim, then edit its hora fim to the Request hora, else create a new horario
        $user = usuario::find(auth()->user()->usuario_id);
        $horario = horario::where('usuario_id', $user->usuario_id)
        //where dia is today
        ->whereRaw("DATE(horario.dia) = CURDATE()")
        ->orderBy('horario_id', 'desc')
        ->first();
        
        // if user already has a horario entrada and saida for today, return error
        if($horario != null and $horario->hora_fim != null){
            return View::make('sistemaPonto.index')->with('erro', 'Você já fez o ponto de entrada e saída para o dia de hoje!');
        }
        // if horario exists

        if($horario != null and $horario->hora_fim == null and  $horario->dia == date('Y-m-d')){
            $datetime = new \DateTime();
            $datetime->setTimezone(new \DateTimeZone('America/Sao_Paulo'));
            $horario->hora_fim = $datetime->format('H:i:s');
            
            $horario->save();
        }else{
            $horario = new horario();
            $horario->dia = date('Y-m-d');
            $datetime = new \DateTime();
            $datetime->setTimezone(new \DateTimeZone('America/Sao_Paulo'));
            $horario->hora_inicio = $datetime->format('H:i:s');
            $horario->usuario_id = $user->usuario_id;
            $horario->save();
        }
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
