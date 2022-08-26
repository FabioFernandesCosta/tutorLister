<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class historicoController extends Controller
{
    //
    public function store(Request $request){
        $historico = new historico();
        $historico->campo_modificado = Request::get('campo_modificado');
        $historico->acao = Request::get('acao');
        $historico->atividade_id = Request::get('atividade_id');
        $historico->usuario_id = Request::get('usuario_id');
        $historico->hora_modificacao = date("Y-m-d");
        $historico->valor_anterior = Request::get('valor_anterior');
        $historico->novo_valor = Request::get('novo_valor');
        $historico->save();
    }

    public function show($id){
        $historico = DB::table('historico')
            ->join('usuario','historico.usuario_id', '=', 'usuario.usuario_id')
            ->select('historico.*', 'usuario.nome', DB::raw("DATE_FORMAT(historico.data_modificacao, '%d/%m/%Y') as data_modificacao"))
            ->where('atividade_id', $id);
        return(datatables($historico)->toJson());
    }
}
