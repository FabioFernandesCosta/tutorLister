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
use Illuminate\Support\Facades\Auth;

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
            'treinamento_concluido' => 'required|in:0,1',
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
            'treinamento_concluido.required' => 'O campo treinamento concluído é obrigatório',
            'treinamento_concluido.in' => 'O campo treinamento concluído deve ser sim ou não',
        ];
        $validator = Validator::make(Request::all(), $rules, $messages);
        
        if ($validator->fails()) {
            return Redirect::to('alunos/create')
            ->withErrors($validator)
            ->withInput(Request::all());
        } else {
            $loctoRed;
            $result = DB::transaction(function () {

                $usuario = new usuario;
                $usuario->email = Request::get('email');
                $usuario->nome = Request::get('nome');
                $usuario->telefone = Request::get('telefone');
                $usuario->ativo = Request::get('ativo');
                $usuario->nivel_de_acesso = Request::get('acesso');
                $usuario->treinamento_concluido = Request::get('treinamento_concluido');
                $usuario->save();
                $loctoRed = $usuario->usuario_id;
                $usuario_curso = new usuario_curso;
                $usuario_curso->usuario_id = $usuario->usuario_id;
                //find curso_id based on curso name (request curso)
                $curso = curso::where('nome', Request::get('curso'))->first();
                $usuario_curso->curso_id = $curso->curso_id;
                $usuario_curso->horario = Request::get('horario');
                $usuario_curso->save();
                
                $user = Auth::user();
                $user_id = $user->usuario_id;
                $historico_controller = new historicoController;
                $historico_controller->store(["", "Usuário criado", $usuario->usuario_id, $user_id, NULL, NULL, 1]);
                
                return ('alunos/' . $usuario->usuario_id);
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
        //
        $usuario = usuario::find($id);
        $usuario_curso = usuario_curso::where('usuario_id', $id)->first();
        $curso = curso::find($usuario_curso->curso_id);
        $usuario->curso = $curso->nome;
        $usuario->horario = $usuario_curso->horario;
        $usuario->ativo = $usuario->ativo == 1 ? 'Sim' : 'Não';
        $usuario->nivel_de_acesso = $usuario->nivel_de_acesso == 1 ? 'Sim' : 'Não';
        $usuario->treinamento_concluido = $usuario->treinamento_concluido == 1 ? 'Sim' : 'Não';


        return (
            View::make('alunos.show')
            ->with('aluno', $usuario)
        );
    }

    public function atvsUser($id){

        $atv = (DB::table('usuario_atividade')
        //only first 25 characters from atividade.descricao
        ->select(DB::raw('SUBSTRING(atividade.descricao, 1, 45) as descricao'), 'atividade.data_atividade', 'atividade.hora_atividade', 'atividade.atividade_id')
        ->join('atividade', 'usuario_atividade.atividade_id', '=', 'atividade.atividade_id')
        ->where('usuario_atividade.usuario_id', $id)
        ->orderBy('atividade.data_atividade', 'desc')
        ->take(17));

        $atv = datatables($atv)->toJson();
        return $atv;
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
        $usuario->treinamento_concluido = $usuario->treinamento_concluido == 1 ? 'Sim' : 'Não';
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
            'treinamento_concluido' => 'required|in:0,1',
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
            'treinamento_concluido.required' => 'O campo treinamento concluído é obrigatório',
            'treinamento_concluido.in' => 'O campo treinamento concluído deve ser sim ou não',
        ];
        $validator = Validator::make(Request::all(), $rules, $messages);
        
        if ($validator->fails()) {
            return Redirect::to('alunos/' . $id . '/edit')
            ->withErrors($validator)
            ->withInput(Request::all());
        } else {
            
            
            $result = DB::transaction(function () use ($id) {
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
                $usuario->treinamento_concluido = Request::get('treinamento_concluido');
                $usuario->save();
                $usuario_curso = usuario_curso::where('usuario_id', $id)->first();
                
                if($usuario_curso->horario != Request::get('horario')){
                    array_push($changedFields[0], 'Horário');
                    array_push($changedFields[1], Request::get('horario'));
                    array_push($changedFields[2], $usuario_curso->horario);
                }
                

                $curso = curso::where('nome', Request::get('curso'))->first();
                if($usuario_curso->curso_id != $curso->curso_id){
                    array_push($changedFields[0], 'Curso');
                    array_push($changedFields[1], $curso->nome);
                    array_push($changedFields[2], curso::find($usuario_curso->curso_id)->nome);
                }

                $usuario_curso->curso_id = $curso->curso_id;
                $usuario_curso->horario = Request::get('horario');
                $usuario_curso->save();

                
                $user = Auth::user();
                $user_id = $user->usuario_id;
                $historico_controller = new historicoController;
                // $historico_controller->store([implode(", ", $changedFields[0]), "editar", $atv->atividade_id, 5, implode(", ", $changedFields[2]), implode(", ", $changedFields[1])]);
                $historico_controller->store([implode(", ", $changedFields[0]), "editar", $usuario->usuario_id, $user_id, implode(", ", $changedFields[2]), implode(", ", $changedFields[1]),1]);
                
                return ('alunos/' . $usuario->usuario_id);
            });
            return Redirect::to($result);
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


    public function import_alunos(Request $request){

        $data = Request::all();
        $data = array_map(null, $data[0], $data[1], $data[2], $data[3], $data[4]);

        
        $rules = array(
            '*.0' => 'required|string|max:255',
            '*.1' => 'required|string|max:255|exists:curso,nome',
            '*.2' => 'nullable|string|max:255|in:Manhã,Tarde,Noite',
            '*.3' => 'required|email|max:255',
            '*.4' => 'nullable|regex:/^\d{2}\s\d{9}$/',
        );
        $messages = array(
            'required' => 'O campo :attribute é obrigatório.',
            'string' => 'O campo :attribute deve ser uma string.',
            'max' => 'O campo :attribute deve ter no máximo :max caracteres.',
            'email' => 'O campo :attribute deve ser um email válido.',
            'in' => 'O campo :attribute deve ser um dos seguintes valores: :values',
            'regex' => 'O campo :attribute deve ser um número de telefone válido.',
            'exists' => 'O campo :attribute deve ser um nome de curso valido',
        );



        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            return Redirect::to('alunos/import')
            ->withErrors($validator)
            ->withInput();
        }else{

            //trata cada elemento de $data como uma atividade para salvar no banco
            $result = DB::transaction(function () use($data) {
                foreach ($data as &$key) {
                    //cria aluno
                    $usuario = new usuario;
                    $usuario->email = $key[3];
                    $usuario->nome = $key[0];
                    $usuario->telefone = $key[4];
                    $usuario->ativo = 1;
                    $usuario->nivel_de_acesso = 0;
                    $usuario->save();


                    //cria usuario_curso
                    $usuario_curso = new usuario_curso;
                    $usuario_curso->usuario_id = $usuario->usuario_id; 
                    $usuario_curso->curso_id = curso::where('nome', $key[1])->first()->curso_id;
                    $usuario_curso->horario = $key[2];
                    $usuario_curso->save();

                    //cria historico
                    $user = Auth::user();
                    $user_id = $user->usuario_id;
                    
                    $historico_controller = new historicoController;
                    $historico_controller->store(["", "Aluno importado", $usuario->usuario_id, $user_id, NULL, NULL, 1]);

                    return ('alunos/');
                    
                }
            });
            return Redirect::to($result);
        }
    }
}
