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
        $filter = Request::get('filter');
        

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
                DB::raw('group_concat(DISTINCT usuario.nome) as nome'))
        ;
        
        if(strtolower($filter) != 'arquivado' and empty($filter)){
            $atv = $atv->where('atividade.status', '<>', 'Arquivado');
        }else if (strtolower($filter) == 'arquivado'){
            $atv = $atv->where('atividade.status', '=', 'Arquivado');
        }

        if (!empty($filter) and strtolower($filter) != 'arquivado') {
            $atv = $atv->orWhere('atividade.atividade_id', '=', $filter)
           ->orWhere('atividade.descricao', 'like', '%'.$filter.'%')
           ->orWhere('usuario.nome', 'like', '%'.$filter.'%')
           ->orWhere('requisitante.nome', 'like', '%'.$filter.'%')
           ->orWhere('atividade.status', 'like', $filter);

        }
        

        
        $atv->groupBy('atividade.atividade_id')
        ->orderBy('atividade.status', 'asc')
        ->orderBy('atividade.atividade_id', 'DESC');
        #add header to the file
        $atv = $atv->get()->toArray();
        array_unshift($atv, $atv[0]);
        $atv[0] = (array) $atv[0];
        $atv[0] = $this->headings();
        return collect($atv);




        // get all
        
        
        //dd($fechado, $arquivado, !empty($filter));

    }

    public function headings(): array
    {
        return ["ID","Data da atividade","Data do registro","Hora da atividade","Hora do registro","Carga","Descrição","Status","Requisitante","Usuarios"];
    }
}
