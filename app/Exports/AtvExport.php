<?php

namespace App\Exports;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Request;

class AtvExport implements FromCollection
{
    protected $filter;
    protected $fechado;
    protected $arquivado;

    function __construct($filter, $fechado, $arquivado) {
        $this->filter = $filter;
        $this->fechado = $fechado;
        $this->arquivado = $arquivado;
        
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $filter = $this->filter;
        $fechadoV = $this->fechado;
        $arquivadoV = $this->arquivado;

        if ($fechadoV == "on") {
            $fechado = "like";
        }else{
            $fechado = "not like";
        }

        if ($arquivadoV == "on") {
            $arquivado = "like";
        }else{
            $arquivado = "not like";
        }


        $atv = DB::table('atividade')
        ->join('usuario_atividade', 'atividade.atividade_id', '=', 'usuario_atividade.atividade_id')
        ->join('usuario','usuario_atividade.usuario_id', '=', 'usuario.usuario_id')
        ->join('atividade_requisitante', 'atividade.atividade_id', 'atividade_requisitante.atividade_id')
        ->join('requisitante', 'atividade_requisitante.requisitante_id', 'requisitante.requisitante_id')
        ->select("atividade.atividade_id", 
                "atividade.data_atividade", 
                "atividade.data_registro", 
                "atividade.hora_atividade", 
                "atividade.hora_registro", 
                "atividade.carga", 
                "atividade.descricao",
                "atividade.status",
                DB::raw('group_concat(DISTINCT requisitante.nome) as requisitante'),
                DB::raw('group_concat(DISTINCT usuario.nome) as nome'));
        


        if (!empty($filter)) {
            $atv = $atv->orWhere('atividade.atividade_id', '=', $filter)
           ->orWhere('atividade.descricao', 'like', '%'.$filter.'%')
           ->orWhere('usuario.nome', 'like', '%'.$filter.'%')
           ->orWhere('requisitante.nome', 'like', '%'.$filter.'%');

        }


        if (Request::get('fec') != "on" and Request::get('arq') != "on") {
            $atv = $atv->whereNotIn('atividade.status', ['Arquivado', 'Fechado']);
        }elseif(empty($filter)){
            $atv = $atv
            ->orWhere('atividade.status', $arquivado, 'Arquivado')
            ->orWhere('atividade.status', $fechado, 'Fechado');
        }
        

        
        $atv->groupBy('atividade.atividade_id')
        ->orderBy('atividade.atividade_id', 'DESC');

        return $atv->get();



        // get all
        
        
        //dd($fechado, $arquivado, !empty($filter));

    }

    public function headings(): array
    {
        return ["ID", "Descrição", "Usuarios", "Requisitante", "Status", "Data da atividade", "Hora da atividade", "Data do registro", "Hora do registro", "Carga"];
    }
}
