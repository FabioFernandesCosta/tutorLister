<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\historico;

class historicoController extends Controller
{
    //
    public function store(array $array){

        $historico = new historico();
        $historico->campo_modificado = $array[0];
        $historico->acao = $array[1];
        $historico->atividade_id = $array[2];
        $historico->usuario_id = $array[3];
        $historico->data_modificacao = date("Y-m-d");
        $historico->valor_anterior = $array[4];
        $historico->novo_valor = $array[5];
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
