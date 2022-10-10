<?php

namespace App\Http\Controllers;


use App\Models\atividade;
use App\Models\dashboard;
use App\Models\curso;
use App\Models\usuario;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get atividades grouped by data_realizacao last 13 months and year
        $atividades = atividade::selectRaw('concat(month(data_atividade), year(data_atividade)) as mes, count(*) as total')
            ->where('data_atividade', '>=', date('Y-m-d', strtotime('-13 months')))
            ->groupBy('mes')
            ->pluck('total', 'mes')->all();

        $atividadesAbertas = atividade::selectRaw('concat(month(data_atividade), year(data_atividade)) as mes, count(*) as total')
            ->where('data_atividade', '>=', date('Y-m-d', strtotime('-13 months')))
            ->where('status', 'Aberto')
            ->groupBy('mes')
            ->pluck('total', 'mes')->all();

        $atividadesEmAndamento = atividade::selectRaw('concat(month(data_atividade), year(data_atividade)) as mes, count(*) as total')
            ->where('data_atividade', '>=', date('Y-m-d', strtotime('-13 months')))
            ->where('status', 'Em andamento')
            ->groupBy('mes')
            ->pluck('total', 'mes')->all();

        $atividadesFechadas = atividade::selectRaw('concat(month(data_atividade), year(data_atividade)) as mes, count(*) as total')
            ->where('data_atividade', '>=', date('Y-m-d', strtotime('-13 months')))
            ->where('status', 'Fechado')
            ->groupBy('mes')
            ->pluck('total', 'mes')->all();


        // Generate random colours for the groups
        $backgroundColours = [];
        $borderColours = [];
        $defaultColors = [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)',
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)',
            'rgba(255, 99, 132, 0.2)',
        ];

        for ($i = 0; $i < 13; $i++) {
            $backgroundColours[] = $defaultColors[$i];
            //borderColours[] need to be = backgroundColours[] but with opacity 1
            $borderColour = explode(',', $defaultColors[$i]);
            $borderColour[3] = ' 1)';
            $borderColours[] = implode(',', $borderColour);
        }
        

        //Prepare the data for returning with the view
        $chart = new dashboard;
        $chart->labels = (array_keys($atividades));

        $meses = array(
            1 => 'Janeiro',
            2 => 'Fevereiro',
            3 => 'Março',
            4 => 'Abril',
            5 => 'Maio',
            6 => 'Junho',
            7 => 'Julho',
            8 => 'Agosto',
            9 => 'Setembro',
            10 => 'Outubro',
            11 => 'Novembro',
            12 => 'Dezembro',
        );

        //label chart with mes name (atvChart)
        $chart->labels = array_map(function ($mes) use ($meses) {
            //split mes last 4 chars = year and first 2 or 1 chars = month
            $mes = substr($mes, -4) . '-' . substr($mes, 0, -4);
            $mes = date('Y-m', strtotime($mes));
            return $meses[date('n', strtotime($mes))] . ' ' . date('Y', strtotime($mes));
        }, $chart->labels);

        $chart->dataset = [
            'Total' => array_values($atividades),
            'Abertas' => array_values($atividadesAbertas),
            'Em andamento' => array_values($atividadesEmAndamento),
            'Fechadas' => array_values($atividadesFechadas),
        ];
        $chart->colours = $backgroundColours;
        $chart->borderColours = $borderColours;


        //alunos (usuario) grouped by curso.nome
        $alunosCursos = usuario::selectRaw('curso.nome as curso, count(*) as total')
            ->join('usuario_curso', 'usuario.usuario_id', '=', 'usuario_curso.usuario_id')
            ->join('curso', 'curso.curso_id', '=', 'usuario_curso.curso_id')
            ->groupBy('curso')
            ->pluck('total', 'curso')->all();

        // Generate random colours for the groups
        $AlunosCursosChart = new dashboard;
        $AlunosCursosChart->labels = (array_keys($alunosCursos));
        $AlunosCursosChart->dataset = [
            'Total' => array_values($alunosCursos),
        ];
        $AlunosCursosChart->colours = $backgroundColours;
        $AlunosCursosChart->borderColours = $borderColours;
        

        
        return view('dashboard', compact('chart'), compact('AlunosCursosChart'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\dasshboard  $dasshboard
     * @return \Illuminate\Http\Response
     */
    public function show(dasshboard $dasshboard)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\dasshboard  $dasshboard
     * @return \Illuminate\Http\Response
     */
    public function edit(dasshboard $dasshboard)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\dasshboard  $dasshboard
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, dasshboard $dasshboard)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\dasshboard  $dasshboard
     * @return \Illuminate\Http\Response
     */
    public function destroy(dasshboard $dasshboard)
    {
        //
    }
}
