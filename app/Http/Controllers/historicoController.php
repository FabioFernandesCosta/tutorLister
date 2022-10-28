<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\historico;
use App\Models\historicoUser;
use App\Models\historicoCurso;
use App\Models\historicoRequisitante;

class historicoController extends Controller
{
    //
    public function store(array $array){
        // [6] = qual tabela
        if($array['6'] == 0){
            $historico = new historico();
            $historico->atividade_id = $array[2];
            $historico->editor = $array[3];
        }elseif($array['6'] == 1){
            $historico = new historicoUser();
            $historico->usuario_id = $array[2];
            $historico->editor = $array[3];
        }elseif ($array['6'] == 2) {
            $historico = new historicoCurso();
            $historico->curso_id = $array[2];
            $historico->editor = $array[3];
        }elseif ($array['6'] == 3) {
            $historico = new historicoRequisitante();
            $historico->requisitante_id = $array[2];
            $historico->editor = $array[3];
        }
        $historico->campo_modificado = $array[0];
        $historico->acao = $array[1];
        $historico->data_modificacao = date("Y-m-d");
        $historico->valor_anterior = $array[4];
        $historico->novo_valor = $array[5];
        $historico->save();
        
        
    }

    public function show($id){
        $historico = DB::table('historico')
            ->join('usuario','historico.editor', '=', 'usuario.usuario_id')
            ->select('historico.*', 'usuario.nome', DB::raw("DATE_FORMAT(historico.data_modificacao, '%d/%m/%Y') as data_modificacao"))
            ->where('atividade_id', $id); //fazer if para identificar quando é em usuario e quando é em atividade
        return(datatables($historico)->toJson());
    }

    public function showUser($id){
        $historico = DB::table('historicoUser')
            ->join('usuario','historicoUser.editor', '=', 'usuario.usuario_id')
            ->select('historicoUser.*', 'usuario.nome', DB::raw("DATE_FORMAT(historicoUser.data_modificacao, '%d/%m/%Y') as data_modificacao"))
            ->where('historicoUser.usuario_id', $id); //fazer if para identificar quando é em usuario e quando é em atividade
        return(datatables($historico)->toJson());
    }

    public function showCurso($id){
        $historico = DB::table('historicoCurso')
            ->join('usuario','historicoCurso.editor', '=', 'usuario.usuario_id')
            ->select('historicoCurso.*', 'usuario.nome', DB::raw("DATE_FORMAT(historicoCurso.data_modificacao, '%d/%m/%Y') as data_modificacao"))
            ->where('historicoCurso.curso_id', $id); //fazer if para identificar quando é em usuario e quando é em atividade
        return(datatables($historico)->toJson());
    }

    public function showRequisitante($id){
        $historico = DB::table('historicoRequisitante')
            ->join('usuario','historicoRequisitante.editor', '=', 'usuario.usuario_id')
            ->select('historicoRequisitante.*', 'usuario.nome', DB::raw("DATE_FORMAT(historicoRequisitante.data_modificacao, '%d/%m/%Y') as data_modificacao"))
            ->where('historicoRequisitante.requisitante_id', $id); //fazer if para identificar quando é em usuario e quando é em atividade
        return(datatables($historico)->toJson());
    }
}
