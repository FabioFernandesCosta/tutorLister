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
        $atividades = atividade::selectRaw('concat(year(data_atividade), date_format(data_atividade, "%m")) as mes, count(*) as total')
            ->where('data_atividade', '>=', date('Y-m-d', strtotime('-13 months')))
            ->groupBy('mes')
            ->pluck('total', 'mes')->all();
            
        $atividadesAbertas = atividade::selectRaw('concat(year(data_atividade), date_format(data_atividade, "%m")) as mes, count(*) as total')
            ->where('data_atividade', '>=', date('Y-m-d', strtotime('-13 months')))
            ->where('status', 'Aberto')
            ->groupBy('mes')
            ->pluck('total', 'mes')->all();

        $atividadesEmAndamento = atividade::selectRaw('concat(year(data_atividade), date_format(data_atividade, "%m")) as mes, count(*) as total')
            ->where('data_atividade', '>=', date('Y-m-d', strtotime('-13 months')))
            ->where('status', 'Em andamento')
            ->groupBy('mes')
            ->pluck('total', 'mes')->all();

        $atividadesFechadas = atividade::selectRaw('concat(year(data_atividade), date_format(data_atividade, "%m")) as mes, count(*) as total')
            ->where('data_atividade', '>=', date('Y-m-d', strtotime('-13 months')))
            ->where('status', 'Fechado')
            ->groupBy('mes')
            ->pluck('total', 'mes')->all();

        $atividadesCanceladas = atividade::selectRaw('concat(year(data_atividade), date_format(data_atividade, "%m")) as mes, count(*) as total')
            ->where('data_atividade', '>=', date('Y-m-d', strtotime('-13 months')))
            ->where('status', 'Cancelado')
            ->groupBy('mes')
            ->pluck('total', 'mes')->all();

        //add a 0 to the beginning of the array key name if it has less than 6 characters	



        //insert into all arrays above the empty months
        for ($i = 0; $i < 13; $i++) {
            $mes = date('Ym', strtotime('-' . $i . ' months'));
            if (!isset($atividades[$mes])) {
                $atividades[$mes] = 0;
            }
            if (!isset($atividadesAbertas[$mes])) {
                $atividadesAbertas[$mes] = 0;
            }
            if (!isset($atividadesEmAndamento[$mes])) {
                $atividadesEmAndamento[$mes] = 0;
            }
            if (!isset($atividadesFechadas[$mes])) {
                $atividadesFechadas[$mes] = 0;
            }
            if (!isset($atividadesCanceladas[$mes])) {
                $atividadesCanceladas[$mes] = 0;
            }
        }

        ksort($atividades);
        ksort($atividadesAbertas);
        ksort($atividadesEmAndamento);
        ksort($atividadesFechadas);
        ksort($atividadesCanceladas);

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
            '01' => 'Janeiro',
            '02' => 'Fevereiro',
            '03' => 'Março',
            '04' => 'Abril',
            '05' => 'Maio',
            '06' => 'Junho',
            '07' => 'Julho',
            '08' => 'Agosto',
            '09' => 'Setembro',
            '10' => 'Outubro',
            '11' => 'Novembro',
            '12' => 'Dezembro',
        );

        //label chart with mes name (atvChart)
        $chart->labels = array_map(function ($mes) use ($meses) {
            //split mes first 4 chars = year and last 2 chars = month (example: 102022 is understand as outubro 2022)
            $year = substr($mes, 0, 4);
            $month = substr($mes, 4, 2);
            return $meses[$month] . ' ' . $year;
        }, $chart->labels);

        //$atividades keys are equal numeric monthYear (example 62022 or 102022) convert it to be only the month

        $chart->dataset = [
            'Total' => array_values($atividades),
            'Abertas' => array_values($atividadesAbertas),
            'Em andamento' => array_values($atividadesEmAndamento),
            'Fechadas' => array_values($atividadesFechadas),
            'Canceladas' => array_values($atividadesCanceladas),
        ];
        $chart->colours = $backgroundColours;
        $chart->borderColours = $borderColours;

        //alunos (usuario) grouped by curso.nome
        $alunosCursos = usuario::selectRaw('curso.nome as curso, count(*) as total')
            ->join('usuario_curso', 'usuario.usuario_id', '=', 'usuario_curso.usuario_id')
            ->join('curso', 'curso.curso_id', '=', 'usuario_curso.curso_id')
            ->where('usuario.ativo', '=', 1)
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


        //10 alunos (usuario) with more atividades tables: usuario, usuario_atividade
        $alunosAtividades = usuario::selectRaw('usuario.nome as aluno, count(*) as total')
            ->join('usuario_atividade', 'usuario.usuario_id', '=', 'usuario_atividade.usuario_id')
            ->groupBy('aluno')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->pluck('total', 'aluno')->all();
        
        // Generate random colours for the groups
        $AlunosAtividadesChart = new dashboard;
        $AlunosAtividadesChart->labels = (array_keys($alunosAtividades));
        $AlunosAtividadesChart->dataset = [
            'Total' => array_values($alunosAtividades),
        ];
        $AlunosAtividadesChart->colours = $backgroundColours;
        $AlunosAtividadesChart->borderColours = $borderColours;

        
        //numero de alunos que concluiram e não concluiram o treinamento
        $alunosTreinoConcluido = usuario::selectRaw('usuario.treinamento_concluido as concluido, count(*) as total')
            ->groupBy('concluido')
            ->pluck('total', 'concluido')->all();

        // Generate random colours for the groups
        $AlunosTreinoConcluidoChart = new dashboard;
        $AlunosTreinoConcluidoChart->labels = array_map(function ($concluido) {
            return $concluido == 0 ? 'Não concluído' : 'Concluído ';
        }, array_keys($alunosTreinoConcluido));
        $AlunosTreinoConcluidoChart->dataset = [
            'Total' => array_values($alunosTreinoConcluido),
        ];
        $AlunosTreinoConcluidoChart->colours = $backgroundColours;
        $AlunosTreinoConcluidoChart->borderColours = $borderColours;


        return view('dashboard')
        ->with('chart', $chart)
        ->with('AlunosCursosChart', $AlunosCursosChart)
        ->with('AlunosAtividadesChart', $AlunosAtividadesChart)
        ->with('AlunosTreinoConcluidoChart', $AlunosTreinoConcluidoChart);
        // , compact('chart'), compact('AlunosAtividadesChart'), compact('AlunosCursosChart'));
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
