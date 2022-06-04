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


    public function index(Request $request)
    {
        // get all
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
        ->where('atividade.atividade_id', '=', $filter)
        ->orWhere('atividade.descricao', 'like', '%'.$filter.'%')
        ->orWhere('usuario.nome', 'like', '%'.$filter.'%')
        ->orWhere('requisitante.nome', 'like', '%'.$filter.'%')
        ->groupBy('atividade.atividade_id')
        ->paginate(15);

        // load the view and pass the data
        return View::make('atividades.index')
            ->with(['atv'=> $atv, 'filter' => $filter]);


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
        try {
            //code...
        
            DB::transaction(function () {
                // validate
                // read more on validation at http://laravel.com/docs/validation
                $rules = array(
                    'InvolvedUsers' => 'required',
                    'DoneData' => 'required',
                    'DoneHour' => 'required',
                    'CargaHoraria' => 'required',
                    'descricao' => 'required',
                    'Requisitante' => 'required',
                    
                );
                $validator = Validator::make(Request::all(), $rules);
                
                    // process the login
                    if ($validator->fails()) {
                        return Redirect::to('atividades/create')
                            ->withErrors($validator)
                            ->withInput(Request::except('password'));
                    } else {
                        //informações para registro da atividade em si
                        $atividade = new atividade;
                        $atividade->data_atividade = Request::get('DoneData');
                        $atividade->hora_atividade = Request::get('DoneHour');
                        $atividade->carga = Request::get('CargaHoraria');
                        $atividade->descricao = Request::get('descricao');
                        $atividade->data_registro = date("Y-m-d");
                        $atividade->hora_registro = date("h:i:s");
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
                        // redirect
                        Session::flash('message', 'Atividade registrada com successo!');
                        $sucesso = true;
                        return Redirect::to('atividades/' . $atividade->atividade_id);
                    }
            });
        } catch (\Throwable $th) {
            echo '<script>alert("Erro, não foi possivel salvar os dados, verifique-os")</script>';
        }

        return Redirect::to('atividades');
        
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
                    'InvolvedUsers' => 'required',
                    'DoneData' => 'required',
                    'DoneHour' => 'required',
                    'CargaHoraria' => 'required',
                    'descricao' => 'required',
                    'Requisitante' => 'required',
                    
                );
                $validator = Validator::make(Request::all(), $rules);
                
                // process the login
                if ($validator->fails()) {
                    return Redirect::to('atividades/'.$id.'/edit')
                        ->withErrors($validator)
                        ->withInput(Request::except('password'));
                } else {
                    DB::transaction(function () use ($id){
                        $atv = atividade::findOrFail($id);
                        $atv->data_atividade = Request::get('DoneData');
                        $atv->hora_atividade = Request::get('DoneHour');
                        $atv->carga = Request::get('CargaHoraria');
                        $atv->descricao = Request::get('descricao');
                        $atv->save();

                        //procura pelos usuarios no banco e se existirem cria a relação com a atividade
                        $invUs = Request::get('InvolvedUsers'); //pega lista de nomes dos usuario envolvidos
                        foreach ($invUs as &$key) {
                            $usuario = '';
                            $usuario = DB::table("usuario")
                            ->select("usuario_id", "nome")
                            ->where("nome", "=", $key)
                            ->get();

                            DB::table("usuario_atividade")
                            ->where("atividade_id", "=", $atv->atividade_id)
                            ->delete();

                            //dd($atv->atividade_id, $usuario[0]->usuario_id);
                            if (strtolower($usuario[0]->nome)== strtolower($key)) {
                                $us_atv = new usuario_atividade;
                                $us_atv->usuario_id = $usuario[0]->usuario_id;

                                $us_atv->atividade_id = $atv->atividade_id;
                            
                                
                            }else{
                                dd($usuario[0]->nome, $key);
                                dd("error, 111");
                            }
                            $us_atv->save();
                        }

                        #falta parte de atualizar requisitante e exibir dados antigos ao entrar na pagina
                        $req = Request::get('Requisitante');
                        $requisitante = DB::table("requisitante") 
                        ->select("requisitante_id","nome")
                        ->where("nome", "=", $req)
                        ->get();
                        if (strtolower($requisitante[0]->nome) == strtolower($req)) {
                            $atv_req = atividade_requisitante::where("atividade_id", "=", $atv->atividade_id)
                            ->Where("requisitante_id","=", $usuario[0]->usuario_id)
                            ->first();


                            $atv_req->requisitante_id = $requisitante[0]->requisitante_id;
                            $atv_req->atividade_id = $atv->atividade_id;
                        }else{
                            dd("error, 111");
                        }

                        $atv_req->save();
                        // redirect
                        Session::flash('message', 'Atividade registrada com successo!');
                        return Redirect::to('atividades');
                    });
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
}
