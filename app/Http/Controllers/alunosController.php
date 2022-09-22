<?php

namespace App\Http\Controllers;

use Request;
use Illuminate\Support\Facades\View;
use Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\atividade;
use App\Models\atividade_requisitante;
use App\Models\requisitante;
use Session;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AtvExport;
use Datatables;
use App\Http\Controllers\historicoController;
use DateTime;
use App\Models\usuario_atividade;
use App\Models\usuario;
use App\Models\usuario_curso;
use App\Models\curso;

class alunosController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getData(Request $request){

        return(datatables(
            DB::table('usuario')
            ->join('usuario_curso', 'usuario.usuario_id', '=', 'usuario_curso.usuario_id')
            ->join('curso', 'usuario_curso.curso_id', '=', 'curso.curso_id')
            ->select('usuario.usuario_id','usuario.nome as usNome', 'usuario.email', 'usuario.telefone', 'usuario.ativo', 'curso.nome as crNome')
            ->groupBy('usuario.usuario_id', 'curso.nome')
        )->toJson());
        //list of returned columns = ['usuario_id', 'nome', 'email', 'telefone', 'ativo', 'nome_curso']
    }
    public function index()
    {
        //
        return View::make('alunos.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return View::make('alunos.create');
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
        $rules = array( 
            'nome' => 'required',
            'email' => 'required|email',
            'telefone' => 'regex:/^\d{2}\s\d{9}$/',
            //check if curso with name exists in the database
            'curso' => 'required|exists:curso,nome',
            'ativo' => 'required|in:0,1',
            'acesso' => 'required|in:0,1',
        );
        $messages = [
            'nome.required' => 'O campo nome é obrigatório',
            'email.required' => 'O campo email é obrigatório',
            'email.email' => 'O campo email deve ser um email válido',
            'telefone.regex' => 'O campo telefone deve ser no formato (xx xxxxxxxxx)',
            'curso.required' => 'O campo curso é obrigatório',
            'curso.exists' => 'O curso informado não existe nos registros',
            'ativo.required' => 'O campo ativo é obrigatório',
            'ativo.in' => 'O campo ativo deve ser sim ou não',
            'acesso.required' => 'O campo acesso é obrigatório',
            'acesso.in' => 'O campo acesso deve ser sim ou não',
        ];
        $validator = Validator::make(Request::all(), $rules, $messages);
        
        if ($validator->fails()) {
            return Redirect::to('alunos/create')
            ->withErrors($validator)
            ->withInput(Request::all());
        } else {
            $loctoRed;
            DB::transaction(function () {

                $usuario = new usuario;
                //dd($usuario);
                //convert telefone into and int
                $usuario->email = Request::get('email');
                $usuario->nome = Request::get('nome');
                $usuario->telefone = str_replace(' ', '', Request::get('telefone'));
                $usuario->ativo = Request::get('ativo');
                $usuario->nivel_de_acesso = Request::get('acesso');
                $usuario->save();
                $loctoRed = $usuario->usuario_id;
                $usuario_curso = new usuario_curso;
                $usuario_curso->usuario_id = $usuario->usuario_id;
                //find curso_id based on curso name (request curso)
                $curso = curso::where('nome', Request::get('curso'))->first();
                $usuario_curso->curso_id = $curso->curso_id;
                $usuario_curso->horario = Request::get('horario');
                $usuario_curso->save();
                
                $historico_controller = new historicoController;
                $historico_controller->store(["", "Usuário criado", $usuario->usuario_id, 5, NULL, NULL, 1]);
                
                //Session::flash('message', 'Aluno cadastrado com sucesso!');
                //dd("test");
                //dd($loctoRed);
                return Redirect::to('alunos/' . $usuario->usuario_id);
            });
            return Redirect::to('alunos/');
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
        $usuario = usuario::find($id);
        $usuario_curso = usuario_curso::where('usuario_id', $id)->first();
        $curso = curso::find($usuario_curso->curso_id);
        $usuario->curso = $curso->nome;
        $usuario->horario = $usuario_curso->horario;
        $usuario->ativo = $usuario->ativo == 1 ? 'Sim' : 'Não';
        $usuario->nivel_de_acesso = $usuario->nivel_de_acesso == 1 ? 'Sim' : 'Não';
        return (View::make('alunos.show')->with('aluno', $usuario));
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
        $usuario = usuario::find($id);
        $usuario_curso = usuario_curso::where('usuario_id', $id)->first();
        $curso = curso::find($usuario_curso->curso_id);
        $usuario->curso = $curso->nome;
        $usuario->horario = $usuario_curso->horario;
        $usuario->ativo = $usuario->ativo == 1 ? 'Sim' : 'Não';
        $usuario->nivel_de_acesso = $usuario->nivel_de_acesso == 1 ? 'Sim' : 'Não';
        return (View::make('alunos.edit')->with('aluno', $usuario));
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
        $rules = array( 
            'nome' => 'required',
            'email' => 'required|email',
            'telefone' => 'regex:/^\d{2}\s\d{9}$/',
            //check if curso with name exists in the database
            'curso' => 'required|exists:curso,nome',
            'ativo' => 'required|in:0,1',
            'acesso' => 'required|in:0,1',
        );
        $messages = [
            'nome.required' => 'O campo nome é obrigatório',
            'email.required' => 'O campo email é obrigatório',
            'email.email' => 'O campo email deve ser um email válido',
            'telefone.regex' => 'O campo telefone deve ser no formato (xx xxxxxxxxx)',
            'curso.required' => 'O campo curso é obrigatório',
            'curso.exists' => 'O curso informado não existe nos registros',
            'ativo.required' => 'O campo ativo é obrigatório',
            'ativo.in' => 'O campo ativo deve ser sim ou não',
            'acesso.required' => 'O campo acesso é obrigatório',
            'acesso.in' => 'O campo acesso deve ser sim ou não',
        ];
        $validator = Validator::make(Request::all(), $rules, $messages);
        
        if ($validator->fails()) {
            return Redirect::to('alunos/' . $id . '/edit')
            ->withErrors($validator)
            ->withInput(Request::all());
        } else {
            
            


            DB::transaction(function () use ($id) {
                //get a deep copy of usuario and usuario_curso before update

                
                

                //update usuario
                $usuario = usuario::find($id);

                $changedFields = array(array(), array(), array());
                if($usuario->nome != Request::get('nome')){
                    array_push($changedFields[0], 'Nome');
                    array_push($changedFields[1], Request::get('nome'));
                    array_push($changedFields[2], $usuario->nome);
                }
                if($usuario->email != Request::get('email')){
                    array_push($changedFields[0], 'Email');
                    array_push($changedFields[1], Request::get('email'));
                    array_push($changedFields[2], $usuario->email);
                }
                if($usuario->telefone != Request::get('telefone')){
                    array_push($changedFields[0], 'Telefone');
                    array_push($changedFields[1], Request::get('telefone'));
                    array_push($changedFields[2], $usuario->telefone);
                }
                if($usuario->ativo != Request::get('ativo')){
                    array_push($changedFields[0], 'Ativo');
                    array_push($changedFields[1], Request::get('ativo'));
                    array_push($changedFields[2], $usuario->ativo);
                }
                if($usuario->nivel_de_acesso != Request::get('acesso')){
                    array_push($changedFields[0], 'Nível de acesso');
                    array_push($changedFields[1], Request::get('acesso'));
                    array_push($changedFields[2], $usuario->nivel_de_acesso);
                }

                $usuario->email = Request::get('email');
                $usuario->nome = Request::get('nome');
                $usuario->telefone = Request::get('telefone');
                $usuario->ativo = Request::get('ativo');
                $usuario->nivel_de_acesso = Request::get('acesso');
                $usuario->save();
                $usuario_curso = usuario_curso::where('usuario_id', $id)->first();
                
                if($usuario_curso->horario != Request::get('horario')){
                    array_push($changedFields[0], 'Horário');
                    array_push($changedFields[1], Request::get('horario'));
                    array_push($changedFields[2], $usuario_curso->horario);
                }
                

                //find curso_id based on curso name (request curso)
                $curso = curso::where('nome', Request::get('curso'))->first();
                if($usuario_curso->curso_id != $curso->curso_id){
                    //dd($usuario_curso->curso_id, $curso->curso_id);
                    array_push($changedFields[0], 'Curso');
                    array_push($changedFields[1], $curso->nome);
                    array_push($changedFields[2], curso::find($usuario_curso->curso_id)->nome);
                }

                $usuario_curso->curso_id = $curso->curso_id;
                $usuario_curso->horario = Request::get('horario');
                $usuario_curso->save();

                
                //dd($changedFields);
                $historico_controller = new historicoController;
                // $historico_controller->store([implode(", ", $changedFields[0]), "editar", $atv->atividade_id, 5, implode(", ", $changedFields[2]), implode(", ", $changedFields[1])]);
                $historico_controller->store([implode(", ", $changedFields[0]), "editar", $usuario->usuario_id, 5, implode(", ", $changedFields[2]), implode(", ", $changedFields[1]),1]);
                
                //Session::flash('message', 'Aluno cadastrado com sucesso!');
                //dd("test");
                //dd($loctoRed);
                return Redirect::to('alunos/' . $usuario->usuario_id);
            });
            return Redirect::to('alunos/');
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
}
