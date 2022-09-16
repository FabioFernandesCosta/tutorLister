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

    public function getData(Request $request){
        //dd(datatables(DB::table('atividade'))->toJson());
        return (datatables(
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
                    DB::raw('group_concat(DISTINCT requisitante.nome) as requisitante'))
                    //DB::raw('group_concat( usuario.nome) as nomeUs'))
                    ->groupBy('atividade.atividade_id')
                    ->addSelect(DB::raw("group_concat( usuario.nome) as nomeUs"))
            
        )->toJson());
        
        
    }

    function array_contains($str, array $arr){
        foreach($arr as $a){
            if(stripos($str, $a) !== false) return true;
        }
        return false;
    }

    public function index(Request $request)
    {
        /*
        $colunas = Request::get('colunas');
        #dd($colunas);
        $filter = Request::get('filter');

        #lida com filtragem avançada
        $filter1 = explode(' ', $filter);
        
        #transforma underline em espaço
        foreach($filter1 as $key=>$f){
            $filter1[$key] = str_replace('_', ' ', $f);
        }
        $filterUsuario = null;
        $filterStatus = null;
        $filterRequisitante = null;
        

        foreach($filter1 as $f){

            if($this->array_contains($f, ['status:', 'usuario:', 'requisitante:'])){
                $f = explode(':', $f);
                if($f[0] == 'status'){
                    $filterStatus = $f[1];
                }elseif($f[0] == 'usuario'){
                    $filterUsuario = $f[1];
                }elseif($f[0] == 'requisitante'){
                    $filterRequisitante = $f[1];
                }elseif($f[0] == 'data'){
                    $filterDataA = $f[1];
                }elseif($f[0] == 'hora'){
                    $filterHoraA = $f[1];
                }
            }
        }
        

        $atv = DB::table('atividade')
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
                "atividade.descricao",
                "atividade.status",
                DB::raw('group_concat(DISTINCT requisitante.nome) as requisitante'),
                DB::raw('group_concat(DISTINCT usuario.nome) as nome'))
        ;
        
        if($filterStatus != null){
            $atv = $atv->where('atividade.status', $filterStatus);
        }else{
            if(strtolower($filter) != 'arquivado' and empty($filter)){
                $atv = $atv->where('atividade.status', '<>', 'Arquivado');
            }else if (strtolower($filter) == 'arquivado'){
                $atv = $atv->where('atividade.status', '=', 'Arquivado');
            }
        }

        #dd(date('Y-m-d', strtotime(ltrim($filterDataA, '>'))).')');
        if($filterUsuario != null){
            $atv = $atv->where('usuario.nome', 'like' , '%'.$filterUsuario.'%');
        }
        if($filterRequisitante != null){
            $atv = $atv->where('requisitante.nome', 'like' , '%'.$filterRequisitante.'%');
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

        $atv2 = $atv->paginate(15);
        //return View::make('atividades.index')->with(['atv'=>Datatables::of($atv)->make(true), 'filter' => $filter, 'colunas' => $colunas]);
        //return Datatables::of($atv)->make(true);

        //return datatables(DB::table('atividade'))->toJson();
        

        // load the view and pass the data
       */ 
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
        $sucesso = false;
        
            //code...
        
        $rules = array(
            'InvolvedUsers' => 'required|exists:usuario,nome',
            'DoneData' => 'required|before:tomorrow',
            'DoneHour' => 'required',
            'CargaHoraria' => 'required',
            'descricao' => 'required',
            'Requisitante' => 'required|exists:requisitante,nome',
            
        );
        $mensagens = array(
            'Requisitante.exists' => 'O valor no campo Requisitante é invalido',
            'InvolvedUsers.exists' => 'O valor no campo Usuarios envolvidos é invalido',
            'DoneData.required' => 'O campo Data da atividade é obrigatorio',
            'Donehour.required' => 'O campo Hora da atividade é obrigatorio',
            'CargaHoraria.required' => 'O campo Carga horária é obrigatorio',
            'descricao.required' => 'O campo Descrição é obrigatorio',
        );
        $validator = Validator::make(Request::all(), $rules, $mensagens);
        // validate
        // read more on validation at http://laravel.com/docs/validation
        
        // process the login
        if ($validator->fails()) {
            return back()
                ->withInput()
                ->withErrors($validator);
        } else {
            DB::transaction(function () {
                //informações para registro da atividade em si
                $atividade = new atividade;
                $atividade->data_atividade = Request::get('DoneData');
                $atividade->hora_atividade = Request::get('DoneHour');
                $atividade->carga = Request::get('CargaHoraria');
                $atividade->descricao = Request::get('descricao');
                $atividade->data_registro = date("Y-m-d");
                $atividade->hora_registro = date("h:i:s");
                $atividade->status = Request::get('status');
                $atividade->save();

                //procura pelos usuarios no banco e se existirem cria a relação com a atividade
                $invUs = Request::get('InvolvedUsers'); //pega lista de nomes dos usuario envolvidos
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
                    
                        
                    }else{
                        dd($usuario[0]->nome, $key);
                        dd("error, 111");
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
                }else{
                    dd("error, 111");
                }
                $atv_req->save();

                $historico_controller = new historicoController;
                $historico_controller->store(["", "criar atividade", $atividade->atividade_id, 5, NULL, NULL]);

                // redirect
                Session::flash('message', 'Atividade registrada com successo!');
                $sucesso = true;
                return Redirect::to('atividades/' . $atividade->atividade_id);
                
            });
            return Redirect::to('atividades/');
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
        //
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
                DB::raw('group_concat(DISTINCT requisitante.requisitante_id) as requisitante'),
                DB::raw('group_concat(DISTINCT usuario.nome) as nome'))
        ->groupBy('atividade.atividade_id')
        ->where('atividade.atividade_id', '=', $id)
        ->get();
        
        $atv[0]->nome = explode(",",$atv[0]->nome);
        $atv[0]->requisitante = RequisitanteController::consultar($atv[0]->requisitante);

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
        //dd($atv->usuarios[0][0]->nome);

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
        
        //

         // validate
                // read more on validation at http://laravel.com/docs/validation
                
                $rules = array(
                    'InvolvedUsers' => 'required|exists:usuario,nome',
                    'DoneData' => 'required',
                    'DoneHour' => 'required',
                    'CargaHoraria' => 'required',
                    'descricao' => 'required',
                    'Requisitante' => 'required|exists:requisitante,nome',
                );
                $mensagens = array(
                    'Requisitante.exists' => 'O valor no campo Requsitante é invalido',
                    'InvolvedUsers.exists' => 'O valor no campo Usuarios envolvidos é invalido',
                    'DoneData.required' => 'O campo Data da atividade é obrigatorio',
                    'Donehour.required' => 'O campo Hora da atividade é obrigatorio',
                    'CargaHoraria.required' => 'O campo Carga horária é obrigatorio',
                    'descricao.required' => 'O campo Descrição horária é obrigatorio',
                );
                $validator = Validator::make(Request::all(), $rules, $mensagens);
                

                if ($validator->fails()) {
                    return Redirect::to('atividades/'.$id.'/edit')
                        ->withInput()
                        ->withErrors($validator);
                } else {
                    DB::transaction(function () use ($id){
                        $atv = atividade::findOrFail($id);


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
                        $atv->save();
                        //procura pelos usuarios no banco e se existirem cria a relação com a atividade
                        $invUs = Request::get('InvolvedUsers'); //pega lista de nomes dos usuario envolvidos
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
                            
                            
                            //dd($atv->atividade_id, $usuario[0]->usuario_id);
                            if (strtolower($usuario[0]->nome)== strtolower($key)) {
                                $us_atv = new usuario_atividade;
                                $us_atv->usuario_id = $usuario[0]->usuario_id;
                                
                                $us_atv->atividade_id = $atv->atividade_id;
                                
                                
                                
                                $us_atv->save();
                            }else{
                                dd($usuario[0]->nome, $key);
                                dd("error, 111");
                            }
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
                            //dd($atv_req);
                            $atv_req->requisitante_id = $requisitante->requisitante_id;
                            $atv_req->atividade_id = $atv->atividade_id;
                        }else{
                            dd("error, 111");
                        }

                        $atv_req->save();

                        $historico_controller = new historicoController;
                        $historico_controller->store([implode(", ", $changedFields[0]), "editar", $atv->atividade_id, 5, implode(", ", $changedFields[2]), implode(", ", $changedFields[1])]);

                        // redirect
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
    public function destroy($id)
    {
        //
    }

    
    public function export(Request $request){
        return Excel::download(new AtvExport(Request::get('filter')), 'atividades.csv');
    }


    public function import_atv(Request $request){

        //save into database the arrays (0 to 6) that comes from Request
        $data = Request::all();
        
        
        //transpoe $data
        //dd($data);
        $data = array_map(null, $data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6]);

        
        foreach ($data as &$key){
            //format key 3 from dd/mm/yyyy to yyyy-mm-dd
            if ($key[3] == null or str_contains($key[3], '/')) {
                $key[3] = str_replace('/', '-', $key[3]);
                $key[3] = date("Y-m-d", strtotime($key[3]));
            }else{
                $key[3] = 'nad';
            }
            //change formated_date from dd-mm-yyyy to yyyy-mm-dd

            if ($key[4] == null or str_contains($key[4], ':')) {
                $key[4] = date("H:i:s", strtotime($key[4]));
            }else{
                $key[4] = 'nad';
            }

            if ($key[5] == null or str_contains($key[5], ':')) {
                $key[5] = date("H:i:s", strtotime($key[5]));
                //dd($key[5]);
            }
        }
        
        //laravel validator rules if $formated_date is a valid date in the format yyyy-mm-dd
        $rules = array(
            '*.0' => 'required|string|max:255',
            '*.1' => 'required|string|max:255',
            //string or null
            '*.2' => 'nullable|string|max:255',
            '*.3' => 'nullable|date_format:Y-m-d|before:today|after:2010-01-01',
            '*.4' => 'nullable|date_format:H:i:s|before:today|after:2010-01-01',
            '*.5' => 'nullable|string|date_format:H:i:s',
            '*.6' => 'nullable|string',
        );

        //laravel validator messages
        $messages = array(
            '*.0.required' => 'O campo "Descrição" é obrigatório.',
            '*.1.required' => 'O campo "Usuarios envolvidos" é obrigatório.',
            '*.3.date_format' => 'O campo "Data" na linha :attribute deve estar no formato "dd/mm/aaaa"',
            '*.4.date_format' => 'O campo "Hora" na linha :attribute deve estar no formato "hh:mm"',
            '*.5.date_format' => 'O campo "carga" na linha :attribute deve estar no formato "hh:mm:ss"',
        );



        $validator = Validator::make($data, $rules, $messages);
        //dd($validator->errors(), $data);
        //if validator fails, redirect to the same page with the errors
        if ($validator->fails()) {
            return Redirect::to('atividades/import')
            ->withErrors($validator)
            ->withInput();
        }else{

            //trata cada elemento de $data como uma atividade para salvar no banco
            DB::transaction(function () use($data) {
                foreach ($data as &$key) {
                    //cria atividade

                    

                    $atv = new atividade;
                    $atv->descricao = $key[0];
                    $atv->data_atividade = $key[3];
                    $atv->hora_atividade = $key[4];
                    $atv->data_registro = date("Y-m-d");
                    $atv->hora_registro = date("h:i:s");
                    $atv->carga = $key[5];
                    $atv->status = $key[6];
                    $atv->save();

                    //procura pelos usuarios no banco e se existirem cria a relação com a atividade
                    $invUs = explode(",", $key[1]); //pega lista de nomes dos usuario envolvidos
                    $ind = 0;
                    foreach ($invUs as &$keyy) {
                        $usuario = '';
                        $usuario = DB::table("usuario")
                        ->select("usuario_id", "nome")
                        ->where("nome", "=", $keyy)
                        ->get();

                        if (strtolower($usuario[0]->nome)== strtolower($keyy)) {
                            $us_atv = new usuario_atividade;
                            $us_atv->usuario_id = $usuario[0]->usuario_id;
                            $us_atv->atividade_id = $atv->atividade_id;
                            $us_atv->save();
                        }else{
                            dd($usuario[0]->nome, $keyy);
                            dd("error, 111");
                        }
                        $ind += 1;
                    }
                    $req = $key[2];
                    $requisitante = DB::table("requisitante") 
                    ->select("requisitante_id","nome")
                    ->where("nome", "=", $req)
                    ->first();

                    //dd($req);
                    if (strtolower($requisitante->nome) == strtolower($req)) {
                        $atv_req = new atividade_requisitante;
                        $atv_req->requisitante_id = $requisitante->requisitante_id;
                        $atv_req->atividade_id = $atv->atividade_id;
                    }else{
                        dd("error, 111");
                    }
                    $atv_req->save();
                    
                    //registra como "atividade importada" no historico
                    $historico_controller = new historicoController;
                    $historico_controller->store(["", "Atividade importada", $atv->atividade_id, 5, NULL, NULL]);

                }
            });
            return Redirect::to('atividades');
        }
    }
}