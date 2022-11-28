<?php

namespace App\Http\Controllers;

use Request;
use Illuminate\Support\Facades\View;
use Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Session;
use DateTime;
use App\Models\curso;
use App\Models\usuario_curso;
use App\Models\usuario;
use Illuminate\Support\Facades\Auth;

class cursoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getData(Request $request)
    {
        //
        return(datatables(
            DB::table('curso')
            ->select('curso_id', 'nome', 'area_curso')
            ->addSelect(DB::raw('(SELECT COUNT(*) FROM usuario_curso WHERE usuario_curso.curso_id = curso.curso_id) as alunos'))
            
        )->toJson());

    }


    public function index()
    {
        return View::make('cursos.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return View::make('cursos.create');
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
        $validator = Validator::make(Request::all(), [
            'nome' => 'required',
            'area_curso' => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect::to('cursos/create')
                ->withErrors($validator)
                ->withInput();
        } else {
            $curso = new curso;
            $curso->nome = Request::get('nome');
            $curso->area_curso = Request::get('area_curso');
            $curso->save();
            $user = Auth::user();
            $user_id = $user->usuario_id;
            $historico_controller = new historicoController;
            // $historico_controller->store(["", "Curso criado", $usuario->usuario_id, $user_id, NULL, NULL, 2]);
            $historico_controller->store(["", "Curso criado", $curso->curso_id, $user_id, NULL, NULL, 2]);

            Session::flash('message', 'Curso cadastrado com sucesso!');
            return Redirect::to('cursos/'.$curso->curso_id.'/edit');
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
        //não necessario

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $curso = curso::find($id);
        return View::make('cursos.edit')->with('curso', $curso);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $validator = Validator::make(Request::all(), [
            'nome' => 'required',
            'area_curso' => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect::to('cursos/' . $id . '/edit')
                ->withErrors($validator)
                ->withInput();
        } else {
            $curso = curso::find($id);
            //find differences between old and new values
            $changedFields = array(array(), array(), array());
            if($curso->nome != Request::get('nome')){
                array_push($changedFields[0], 'Nome');
                array_push($changedFields[1], Request::get('nome'));
                array_push($changedFields[2], $curso->nome);
            }
            if($curso->area_curso != Request::get('area_curso')){
                array_push($changedFields[0], 'Area do curso');
                array_push($changedFields[1], Request::get('area_curso'));
                array_push($changedFields[2], $curso->area_curso);
            }

            $curso->nome = Request::get('nome');
            $curso->area_curso = Request::get('area_curso');
            $curso->save();
            
            $user = Auth::user();
            $user_id = $user->usuario_id;
            $historico_controller = new historicoController;
            $historico_controller->store([implode(", ", $changedFields[0]), "Editar", $curso->curso_id, $user_id, implode(", ", $changedFields[2]), implode(", ", $changedFields[1]),2]);



            Session::flash('message', 'Curso atualizado com sucesso!');
            return Redirect::to('cursos/' . $id . '/edit');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    

    public function import_cursos(Request $request){
        $data = Request::all();
        $data = array_map(null, $data[0], $data[1]);
        $rules = array(
            '*.0' => 'required',
            '*.1' => 'required',
        );
        $messages = array(
            '*.0.required' => 'O campo nome é obrigatório',
            '*.1.required' => 'O campo curso é obrigatório',
        );
        
        
        
        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            return Redirect::to('cursos/import')
            ->withErrors($validator)
            ->withInput();
        }else{
            
            //trata cada elemento de $data como uma atividade para salvar no banco
            DB::transaction(function () use($data) {
                foreach ($data as &$key) {
                    //cria curso
                    $curso = new curso;
                    $curso->nome = $key[0];
                    $curso->area_curso = $key[1];
                    $curso->save();
                }
            });
            return Redirect::to('cursos');
        }
    
    }
}
