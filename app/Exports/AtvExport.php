<?php

namespace App\Exports;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Request;

class AtvExport implements FromCollection
{
    protected $filter;

    function __construct($filter) {
        $this->filter = $filter;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $filter = $this->filter;
        return DB::table('atividade')
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
        ->where('atividade.atividade_id', '=', $filter)
        ->orWhere('atividade.descricao', 'like', '%'.$filter.'%')
        ->orWhere('usuario.nome', 'like', '%'.$filter.'%')
        ->orWhere('requisitante.nome', 'like', '%'.$filter.'%')
        ->groupBy('atividade.atividade_id')
        ->get();
    }

    public function headings(): array
    {
        return ["ID", "Descrição", "Usuarios", "Requisitante", "Status", "Data da atividade", "Hora da atividade", "Data do registro", "Hora do registro", "Carga"];
    }
}
