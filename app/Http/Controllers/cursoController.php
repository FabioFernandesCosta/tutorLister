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
        $validator = Validator::make($request->all(), [
            'nome' => 'required',
            'area_curso' => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect::to('cursos/create')
                ->withErrors($validator)
                ->withInput();
        } else {
            $curso = new curso;
            $curso->nome = $request->nome;
            $curso->area_curso = $request->area_curso;
            $curso->save();

            Session::flash('message', 'Curso cadastrado com sucesso!');
            return Redirect::to('cursos');
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
        $validator = Validator::make($request->all(), [
            'nome' => 'required',
            'area_curso' => 'required',
        ]);

        if ($validator->fails()) {
            return Redirect::to('cursos/' . $id . '/edit')
                ->withErrors($validator)
                ->withInput();
        } else {
            DB::transaction(function () use ($request, $id) {
                $curso = curso::find($id);
                $curso->nome = $request->nome;
                $curso->area_curso = $request->area_curso;
                $curso->save();
            });
            Session::flash('message', 'Curso atualizado com sucesso!');
            return Redirect::to('cursos');
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
        //não precisa
    }

    public function import_cursos(Request $request){
        //save into database the arrays (0 to 6) that comes from Request
        $data = Request::all();
        
        
        //transpoe $data
        //dd($data);
        //[nome, curso, horario, email, telefone]
        $data = array_map(null, $data[0], $data[1]);
        
        
        //laravel validator rules if $formated_date is a valid date in the format yyyy-mm-dd
        $rules = array(
            '*.0' => 'required',
            '*.1' => 'required',
        );
        
        //laravel validator messages
        $messages = array(
            '*.0.required' => 'O campo nome é obrigatório',
            '*.1.required' => 'O campo curso é obrigatório',
        );
        
        
        
        $validator = Validator::make($data, $rules, $messages);
        //dd($validator->errors(), $data);
        //if validator fails, redirect to the same page with the errors
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
           // dd($data);
            return Redirect::to('cursos');
        }
    
    }
}
