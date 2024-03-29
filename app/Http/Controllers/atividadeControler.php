<?php

namespace App\Http\Controllers;

use Request;
use Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use App\Models\atividade;
use App\Models\usuario_atividade;
use App\Models\usuario;
use App\Models\atividade_requisitante;
use App\Models\requisitante;
use Session;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AtvExport;
use Datatables;
use App\Http\Controllers\historicoController;
use DateTime;
use Illuminate\Support\Facades\Auth;
use DateTimeZone;


// Classe de controle de atividades
class atividadeControler extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */




    public function consultar(Request $request)
    {
        $search = str_replace(['.', '#'], '' , explode('_', Request::get('term')));
        $result = DB::table(strtolower($search[0]))->where('nome', 'LIKE', '%'. $search[1]. '%')->pluck('nome');
        return response()->json($result);
            
    }

    // retorna as atividades em formato json para serem usadas no datatables
    public function getData(Request $request){
        
        $data = ((
            DB::table('atividade')
            ->join('usuario_atividade', 'atividade.atividade_id', '=', 'usuario_atividade.atividade_id')
            ->join('usuario','usuario_atividade.usuario_id', '=', 'usuario.usuario_id')
            ->join('atividade_requisitante', 'atividade.atividade_id', 'atividade_requisitante.atividade_id')
            ->join('requisitante', 'atividade_requisitante.requisitante_id', 'requisitante.requisitante_id')
            ->select("atividade.atividade_id", 
                    DB::raw("DATE_FORMAT(atividade.data_atividade, '%d/%m/%Y') as data_atividade"),
                    DB::raw("DATE_FORMAT(atividade.data_registro, '%d/%m/%Y') as data_registro"),
                    DB::raw("TIME_FORMAT(atividade.hora_atividade, '%H:%i') as hora_atividade"),
                    DB::raw("TIME_FORMAT(atividade.hora_registro, '%H:%i') as hora_registro"),
                    DB::raw("TIME_FORMAT(atividade.carga, '%H:%i') as carga"),
                    DB::raw("SUBSTR(atividade.descricao, 1, 64) as descricao"),
                    "atividade.status",
                    DB::raw('group_concat(DISTINCT requisitante.nome ) as requisitante'))
                    //DB::raw('group_concat( usuario.nome) as nomeUs'))
                    ->groupBy('atividade.atividade_id')
                    ->addSelect(DB::raw("group_concat(usuario.nome SEPARATOR ', ') as nomeUs"))
                    ->addSelect(DB::raw("group_concat(usuario.usuario_id SEPARATOR ', ') as IDUs"))
                    
                    ->where(function($query){
                        if(Auth::user()->npi==true and Auth::user()->aluno_tutor==true){
                            $query->where('atividade.organizacao', '=', strtolower('npi'));
                            $query->orWhere('atividade.organizacao', '=', strtolower('aluno_tutor'));
                        }else if(Auth::user()->npi==true){
                            $query->where('atividade.organizacao', '=', strtolower('npi'));
                        }else if(Auth::user()->aluno_tutor==true){
                            $query->where('atividade.organizacao', '=', strtolower('aluno_tutor'));
                        }
                    })

                    
                    

            
        ));
        $min = strtotime(Request::get("min"));
        $max = strtotime(Request::get("max"));
        
        
        if ($min != null && $max == null) {
            $min = date("Y-m-d", ($min) );
            $data = $data->whereRaw (("DATE(atividade.data_atividade) >= '".($min)."'"));
        }

        elseif ($min == null && $max != null) {
            $max = date("Y-m-d", ($max) );
            $data = $data->whereRaw (("DATE(atividade.data_atividade) <= '".($max)."'"));
        }

        elseif ($min != null && $max != null) {
            $min = date("Y-m-d", ($min) );
            $max = date("Y-m-d", ($max) );
            $data = $data->whereRaw (("DATE(atividade.data_atividade) between '".($min)."' and '".($max)."'"));
        }


        return datatables($data)->toJson();
    }

    function array_contains($str, array $arr){
        foreach($arr as $a){
            if(stripos($str, $a) !== false) return true;
        }
        return false;
    }

    public function index(Request $request)
    {
        
        return View::make('atividades.index');


    }

    


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return View::make('atividades.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = array(
            'InvolvedUsers' => 'required|exists:usuario,nome',
            'DoneData' => 'required|before:tomorrow',
            'DoneHour' => 'required',
            'CargaHoraria' => 'required',
            'descricao' => 'required',
            'Requisitante' => 'required|exists:requisitante,nome',
            'status' => 'required|in:Aberto,Fechado,Em andamento,Arquivado,Cancelado',
            'organizacao' => 'required|in:npi,aluno tutor',
        );
        $mensagens = array(
            'Requisitante.exists' => 'O valor no campo Requisitante é invalido',
            'InvolvedUsers.exists' => 'O valor no campo Usuarios envolvidos é invalido',
            'DoneData.required' => 'O campo Data da atividade é obrigatorio',
            'Donehour.required' => 'O campo Hora da atividade é obrigatorio',
            'CargaHoraria.required' => 'O campo Carga horária é obrigatorio',
            'descricao.required' => 'O campo Descrição é obrigatorio',
            'status.required' => 'O campo Status é obrigatorio',
            'organizacao.required' => 'O campo Organização é obrigatorio',
            'status.in' => 'O valor no campo Status é invalido',
            'organizacao.in' => 'O valor no campo Organização é invalido',


        );
        $validator = Validator::make(Request::all(), $rules, $mensagens);
        
        if ($validator->fails()) {
            return back()
                ->withInput()
                ->withErrors($validator);
        } else {
            $result = DB::transaction(function () {
                // if organizacao = "aluno tutor" set it to "aluno_tutor"
                $organizacao = Request::get('organizacao');
                if($organizacao == "aluno tutor"){
                    $organizacao = "aluno_tutor";
                }
                $atividade = new atividade;
                $atividade->data_atividade = Request::get('DoneData');
                $atividade->hora_atividade = Request::get('DoneHour');
                $atividade->carga = Request::get('CargaHoraria');
                $atividade->descricao = Request::get('descricao');

                $date = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));

                $atividade->data_registro = $date->format('Y-m-d');
                $atividade->hora_registro = $date->format('H:i:s');


                $atividade->status = Request::get('status');
                $atividade->organizacao = strtolower($organizacao);
                $atividade->save();

                
                $invUs = Request::get('InvolvedUsers'); 
                
                foreach ($invUs as &$key) {
                    $usuario = '';
                    $usuario = DB::table("usuario")
                    ->select("usuario_id", "nome")
                    ->where("nome", "=", $key)
                    ->get();
                    if (strtolower($usuario[0]->nome)== strtolower($key)) {
                        $us_atv = new usuario_atividade;
                        $us_atv->usuario_id = $usuario[0]->usuario_id;
                        $us_atv->atividade_id = $atividade->atividade_id;
                    
                        
                    }
                    $us_atv->save();
                }

                $req = Request::get('Requisitante');
                $requisitante = DB::table("requisitante") 
                ->select("requisitante_id","nome")
                ->where("nome", "=", $req)
                ->get();
                if (strtolower($requisitante[0]->nome) == strtolower($req)) {
                    $atv_req = new atividade_requisitante;
                    $atv_req->requisitante_id = $requisitante[0]->requisitante_id;
                    $atv_req->atividade_id = $atividade->atividade_id;
                }
                $atv_req->save();

                $user = Auth::user();
                $user_id = $user->usuario_id;

                $historico_controller = new historicoController;
                $historico_controller->store(["", "Criar atividade", $atividade->atividade_id, $user_id, NULL, NULL, 0]);

                Session::flash('message', 'Atividade registrada com successo!');
                return ('atividades/' . $atividade->atividade_id);
                
            });
            return Redirect::to($result);
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
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
                "atividade.organizacao",
                DB::raw('group_concat(DISTINCT requisitante.requisitante_id) as requisitante'),
                DB::raw('group_concat(DISTINCT usuario.nome) as nome'),
                DB::raw('group_concat(DISTINCT usuario.usuario_id) as usId'))
        ->groupBy('atividade.atividade_id')
        ->where('atividade.atividade_id', '=', $id)
        ->get();
        
        $atv[0]->nome = explode(",",$atv[0]->nome);
        // add to $atv[0] the id equivalent to the user name
        $atv[0]->nomeId = explode(",",$atv[0]->usId);



        $atv[0]->requisitante = requisitante::find($atv[0]->requisitante);

        //change atv[0]->organizacao to Aluno Tutor if it is aluno_tutor and to NPI if it is npi
        if($atv[0]->organizacao == "aluno_tutor"){
            $atv[0]->organizacao = "Aluno Tutor";
        }else if($atv[0]->organizacao == "npi"){
            $atv[0]->organizacao = "NPI";
        }

        return View::make('atividades.show') ->with('atv', $atv);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
        $atv = atividade::find($id);

        $ar = atividade_requisitante::where('atividade_id','=',$atv->atividade_id)->get();
        $rq = requisitante::where('requisitante_id', '=', $ar[0]->requisitante_id)->get();
        $atv->requisitante = $rq[0];

        $ua = usuario_atividade::where('atividade_id','=',$atv->atividade_id)->get();
        $us = usuario::where('usuario_id', '=', $ua[0]->usuario_id)->get();
        $arr = [];
        foreach ($ua as $key => $value) {
            $us = usuario::where('usuario_id', '=', $value->usuario_id)->get();
            array_push($arr, $us[0]);
        }
        $atv->usuarios = $arr;

        return View::make('atividades.edit')->with('atv',$atv);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
                
                $rules = array(
                    'InvolvedUsers' => 'required|exists:usuario,nome',
                    'DoneData' => 'required',
                    'DoneHour' => 'required',
                    'CargaHoraria' => 'required',
                    'descricao' => 'required',
                    'Requisitante' => 'required|exists:requisitante,nome',
                    'status' => 'required|in:Aberto,Fechado,Em andamento,Arquivado,Cancelado',
                    'organizacao' => 'required|in:npi,aluno tutor',


                );
                $mensagens = array(
                    'Requisitante.exists' => 'O valor no campo Requsitante é invalido',
                    'InvolvedUsers.exists' => 'O valor no campo Usuarios envolvidos é invalido',
                    'DoneData.required' => 'O campo Data da atividade é obrigatorio',
                    'Donehour.required' => 'O campo Hora da atividade é obrigatorio',
                    'CargaHoraria.required' => 'O campo Carga horária é obrigatorio',
                    'descricao.required' => 'O campo Descrição horária é obrigatorio',
                    'status.required' => 'O campo Status é obrigatorio',
                    'organizacao.required' => 'O campo Organização é obrigatorio',
                    'status.in' => 'O valor no campo Status é invalido',
                    'organizacao.in' => 'O valor no campo Organização é invalido',
                );
                $validator = Validator::make(Request::all(), $rules, $mensagens);
                

                if ($validator->fails()) {
                    return Redirect::to('atividades/'.$id.'/edit')
                        ->withInput()
                        ->withErrors($validator);
                } else {
                    DB::transaction(function () use ($id){
                        $atv = atividade::findOrFail($id);
                        $organizacao = Request::get('organizacao');
                        if($organizacao == "aluno tutor"){
                            $organizacao = "aluno_tutor";
                        }


                        $changedFields = array(array(), array(), array());
                        if ($atv->data_atividade != Request::get('DoneData')) {
                            array_push($changedFields[0], 'Data da atividade');
                            array_push($changedFields[1], Request::get('DoneData'));
                            array_push($changedFields[2], $atv->data_atividade);
                            
                        }

                        if ($atv->hora_atividade != Request::get('DoneHour')) {
                            array_push($changedFields[0], 'Hora da atividade');
                            array_push($changedFields[1], Request::get('DoneHour'));
                            array_push($changedFields[2], $atv->hora_atividade);
                        }

                        // same as above but for organizacao
                        if ($atv->organizacao != $organizacao) {
                            array_push($changedFields[0], 'Organização');
                            array_push($changedFields[1], $organizacao);
                            array_push($changedFields[2], $atv->organizacao);
                        }

                        // check if the atividade_requisitante and Request::get('Requisitante') are the same
                        $ar = atividade_requisitante::where('atividade_id', '=', $atv->atividade_id)->get();
                        $rq = requisitante::where('requisitante_id', '=', $ar[0]->requisitante_id)->get();
                        if ($rq[0]->nome != Request::get('Requisitante')) {
                            array_push($changedFields[0], 'Requisitante');
                            array_push($changedFields[1], Request::get('Requisitante'));
                            array_push($changedFields[2], $rq[0]->nome);
                        }

                        
                        
                        // historico usuario adicionado
                        $usuarios = Request::get('InvolvedUsers');
                        // find id usuario
                        $user_id = [];
                        foreach ($usuarios as $key => $value) {
                            $user = usuario::where('nome', '=', $value)->get();
                            array_push($user_id, [$user[0]->usuario_id, $value]);
                        }
                        // for each id usuario
                        // if theres no usuario_atividade with the id of the user and the id of this atividade
                        if (count($user_id) > 0) {
                            foreach ($user_id as $key => $value) {
                                $ua = usuario_atividade::where('usuario_id', '=', $value[0])->where('atividade_id', '=', $atv->atividade_id)->get();
                                if (count($ua) == 0) {
                                    $historico_addUser = new historicoController;
                                    // get usuario name
                                    // dd($value[1]);
                                    // dd type of value[1]
                                    $user = Auth::user();
                                    $userr_id = $user->usuario_id;

                                    $historico_addUser->store([$value[1], "Adicionar aluno", $atv->atividade_id, $userr_id, NULL, NULL, 0]);

                                }
                            }
                        }

                        // historico usuario removido
                        $ua = usuario_atividade::where('atividade_id', '=', $atv->atividade_id)->get();
                        // find usuario_nome
                        $user_nome = [];
                        foreach ($ua as $key => $value) {
                            $user = usuario::where('usuario_id', '=', $value->usuario_id)->get();
                            array_push($user_nome, [$user[0]->nome, $value->usuario_id]);
                        }
                        // compare $user_id with $user_nome and do ($historico_addUser->store([$value[1], "Remover aluno", $atv->atividade_id, $userr_id, NULL, NULL, 0]);) for each user that is not in $user_id
                        if (count($user_nome) > 0) {
                            foreach ($user_nome as $key => $value) {
                                $found = false;
                                foreach ($user_id as $key2 => $value2) {
                                    if ($value[0] == $value2[1]) {
                                        $found = true;
                                    }
                                }
                                if ($found == false) {
                                    $historico_addUser = new historicoController;
                                    // get usuario name
                                    // dd($value[1]);
                                    // dd type of value[1]
                                    $user = Auth::user();
                                    $userr_id = $user->usuario_id;

                                    $historico_addUser->store([$value[0], "Remover aluno", $atv->atividade_id, $userr_id, NULL, NULL, 0]);
                                }
                            }
                        }
                        

                        
                        

                        


                        

                        if ($atv->carga != Request::get('CargaHoraria')) {
                            array_push($changedFields[0], 'Carga Horária');
                            array_push($changedFields[1], Request::get('CargaHoraria'));
                            array_push($changedFields[2], $atv->carga);

                        }

                        if ($atv->descricao != Request::get('descricao')) {
                            array_push($changedFields[0], 'Descrição');
                            array_push($changedFields[1], Request::get('descricao'));
                            array_push($changedFields[2], 
                            $atv->descricao);
                        }

                        if ($atv->status != Request::get('status')) {
                            array_push($changedFields[0], 'Status');
                            array_push($changedFields[1], Request::get('status'));
                            array_push($changedFields[2], $atv->status);
                        }

                        
                        $atv->data_atividade = Request::get('DoneData');
                        $atv->hora_atividade = Request::get('DoneHour');
                        $atv->carga = Request::get('CargaHoraria');
                        $atv->descricao = Request::get('descricao');
                        $atv->status = Request::get('status');
                        $atv->organizacao = strtolower($organizacao);
                        $atv->save();
                        $invUs = Request::get('InvolvedUsers'); 
                        $ind = 0;

                        
                        DB::table("usuario_atividade")
                        ->where("atividade_id", "=", $atv->atividade_id)
                        ->delete();
                        foreach ($invUs as &$key) {
                            $usuario = '';
                            $usuario = DB::table("usuario")
                            ->select("usuario_id", "nome")
                            ->where("nome", "=", $key)
                            ->get();
                            if (strtolower($usuario[0]->nome)== strtolower($key)) {
                                $us_atv = new usuario_atividade;
                                $us_atv->usuario_id = $usuario[0]->usuario_id;
                                $us_atv->atividade_id = $atv->atividade_id;
                            }
                            $us_atv->save();
                            $ind += 1;
                        }


                        
                        $req = Request::get('Requisitante');
                        $requisitante = DB::table("requisitante") 
                        ->select("requisitante_id","nome")
                        ->where("nome", "=", $req)
                        ->first();

                        if ($requisitante->nome != $req) {
                            array_push($changedFields[0], 'requisitante');
                            array_push($changedFields[1], $req);
                            array_push($changedFields[2], $requisitante->nome);
                        }

                        if (strtolower($requisitante->nome) == strtolower($req)) {
                            $atv_req = atividade_requisitante::where("atividade_id", "=", $atv->atividade_id)
                            ->first();
                            $atv_req->requisitante_id = $requisitante->requisitante_id;
                            $atv_req->atividade_id = $atv->atividade_id;
                        }

                        $atv_req->save();
                        $user = Auth::user();
                        $user_id = $user->usuario_id;

                        $historico_controller = new historicoController;
                        $historico_controller->store([implode(", ", $changedFields[0]), "Editar", $atv->atividade_id, $user_id, implode(", ", $changedFields[2]), implode(", ", $changedFields[1]),0]);

                        Session::flash('message', 'Atividade registrada com successo!');
                    });
                    return Redirect::to('atividades/'.$id);
                }

    }

    


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    
    public function export(Request $request){
        return Excel::download(new AtvExport(Request::get('filter')), 'atividades.csv');
    }


    public function import_atv(Request $request){
        $data = Request::all();
        
        
        $data = array_map(null, $data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6],$data[7]);

        
        foreach ($data as &$key){
            if ($key[3] == null or str_contains($key[3], '/')) {
                $key[3] = str_replace('/', '-', $key[3]);
                $key[3] = date("Y-m-d", strtotime($key[3]));
            }else{
                $key[3] = 'nad';
            }

            if ($key[4] == null or str_contains($key[4], ':')) {
                $key[4] = date("H:i:s", strtotime($key[4]));
            }else{
                $key[4] = 'nad';
            }

            if ($key[5] == null or str_contains($key[5], ':')) {
                $key[5] = date("H:i:s", strtotime($key[5]));
            }
        }

        
        $rules = array(
            '*.0' => 'required|string|max:255',
            '*.1' => 'required|string|max:255',
            //string or null
            '*.2' => 'required|string|max:255',
            // before tomorrow
            '*.3' => 'nullable|date_format:Y-m-d|after:2010-01-01|before:tomorrow',
            '*.4' => 'nullable|date_format:H:i:s|before:now',
            '*.5' => 'nullable|string|date_format:H:i:s',
            '*.6' => 'nullable|string',
            //7 is = NPI or Aluno tutor
            '*.7' => 'nullable|string|max:255|in:NPI,Aluno tutor,',


        );

        $messages = array(
            '*.0.required' => 'O campo "Descrição" é obrigatório.',
            '*.1.required' => 'O campo "Usuarios envolvidos" é obrigatório.',
            '*.2.required' => 'O campo "Requisitante" é obrigatório.',
            '*.3.date_format' => 'O campo "Data" na linha :attribute deve estar no formato "dd/mm/aaaa"',
            '*.4.date_format' => 'O campo "Hora" na linha :attribute deve estar no formato "hh:mm"',
            '*.5.date_format' => 'O campo "carga" na linha :attribute deve estar no formato "hh:mm:ss"',
            '*.7.in' => 'O campo "NPI ou Aluno tutor" na linha :attribute deve ser "NPI" ou "Aluno tutor"',

        );



        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            return Redirect::to('atividades/import')
            ->withErrors($validator)
            ->withInput();
        }else{

            //trata cada elemento de $data como uma atividade para salvar no banco
            $result = DB::transaction(function () use($data) {
                foreach ($data as &$key) {
                    //cria atividade
                    $atv = new atividade;
                    $atv->descricao = $key[0];
                    $atv->data_atividade = $key[3];
                    $atv->hora_atividade = $key[4];
                    $atv->data_registro = date("Y-m-d");
                    $atv->hora_registro = date("h:i:s");
                    
                    $atv->carga = $key[5];
                    // if key[6] is null, set status to 'Aberto'

                    $atv->status = ($key[6] == null) ? 'Aberto' : $key[6];

                    // $atv->organizacao = ($key[7] = null) ? ((Auth::user()->npi == 1) ? 'npi' : 'aluno tutor' : ($key[7] == 'NPI') ? 'npi' : (key[7] == 'Aluno tutor') ? 'aluno_tutor') : 'npi');
                    // if key[7] is null, (if user is npi, set organizacao to 'npi', else (if user is aluno tutor set to 'aluno tutor')) if key[7] is 'NPI', set organizacao to 'npi', if key[7] is 'Aluno tutor', set organizacao to 'aluno tutor'
                    if ($key[7] == null) {
                        if (Auth::user()->npi == 1) {
                            $atv->organizacao = 'npi';
                        }else if (Auth::user()->aluno_tutor == 1) {
                            $atv->organizacao = 'aluno_tutor';
                        }
                    }else if (strtolower($key[7]) == 'npi') {
                        $atv->organizacao = 'npi';
                    }else if (strtolower($key[7]) == 'aluno tutor' or strtolower($key[7]) == 'aluno_tutor') {
                        $atv->organizacao = 'aluno_tutor';
                    }



                    $atv->save();

                    //procura pelos usuarios no banco e se existirem cria a relação com a atividade
                    $invUs = explode(", ", $key[1]); //pega lista de nomes dos usuario envolvidos
                    $ind = 0;
                    foreach ($invUs as &$keyy) {
                        $usuario = '';
                        $usuario = DB::table("usuario")
                        ->select("usuario_id", "nome")
                        ->where("nome", "=", $keyy)
                        ->get();


                        if (strtolower($usuario[0]->nome) == strtolower($keyy)) {
                            $us_atv = new usuario_atividade;
                            $us_atv->usuario_id = $usuario[0]->usuario_id;
                            $us_atv->atividade_id = $atv->atividade_id;
                            $us_atv->save();
                            
                        }
                        $ind += 1;

                    }
                    $req = $key[2];
                    $requisitante = DB::table("requisitante") 
                    ->select("requisitante_id","nome")
                    ->where("nome", "=", $req)
                    ->first();
                    // and req not null
                    if (($req != null) and (strtolower($requisitante->nome) == strtolower($req))) {
                        $atv_req = new atividade_requisitante;
                        $atv_req->requisitante_id = $requisitante->requisitante_id;
                        $atv_req->atividade_id = $atv->atividade_id;
                        $atv_req->save();
                    }
                    
                    $user = Auth::user();
                    $user_id = $user->usuario_id;
                    //registra como "atividade importada" no historico
                    $historico_controller = new historicoController;
                    $historico_controller->store(["", "Atividade importada", $atv->atividade_id, $user_id, NULL, NULL, 0]);

                    return ('atividades/');
                }
            });
            return Redirect::to($result);
        }
    }
}