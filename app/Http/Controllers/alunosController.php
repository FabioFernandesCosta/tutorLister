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
            ->select('usuario.usuario_id','usuario.nome as usNome', 'usuario.email', 'usuario.telefone', 'curso.nome as crNome')
            ->groupBy('usuario.usuario_id', 'curso.nome')
        )->toJson());
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
            'acesso' => 'required|in:0,1,2,3',
            'treinamento_concluido' => 'required|in:0,1,2,3',
            'npi' => 'required|in:0,1',
            'aluno_tutor' => 'required|in:0,1',
        );
        $messages = [
            'nome.required' => 'O campo nome é obrigatório',
            'email.required' => 'O campo email é obrigatório',
            'email.email' => 'O campo email deve ser um email válido',
            'telefone.regex' => 'O campo telefone deve ser no formato (xx xxxxxxxxx)',
            'curso.required' => 'O campo curso é obrigatório',
            'curso.exists' => 'O curso informado não existe nos registros',
            'acesso.required' => 'O campo acesso é obrigatório',
            'acesso.in' => 'O campo acesso deve ser: NPI, Aluno Tutor, ambos ou nenhum',
            'treinamento_concluido.required' => 'O campo treinamento concluído é obrigatório',
            'treinamento_concluido.in' => 'O campo treinamento concluído deve ser sim ou não',
            'npi.required' => 'O campo NPI é obrigatório',
            'npi.in' => 'O campo NPI deve ser sim ou não',
            'aluno_tutor.required' => 'O campo aluno tutor é obrigatório',
            'aluno_tutor.in' => 'O campo aluno tutor deve ser sim ou não',
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
                $usuario->nivel_de_acesso = Request::get('acesso');
                $usuario->treinamento_concluido = Request::get('treinamento_concluido');
                $usuario->npi = Request::get('npi');
                $usuario->aluno_tutor = Request::get('aluno_tutor');
                $usuario->save();
                $loctoRed = $usuario->usuario_id;
                $usuario_curso = new usuario_curso;
                $usuario_curso->usuario_id = $usuario->usuario_id;
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
        if (Auth::user()->admin != 1) {
            if (Auth::user()->usuario_id != $id) {
                return Redirect::to('dashboard');
            }
        }
        $usuario = usuario::find($id);
        $usuario_curso = usuario_curso::where('usuario_id', $id)->first();
        $curso = curso::find($usuario_curso->curso_id);
        $usuario->curso = $curso->nome;
        $usuario->horario = $usuario_curso->horario;
        $usuario->npi = $usuario->npi == 1 ? 'Sim' : 'Não';
        $usuario->aluno_tutor = $usuario->aluno_tutor == 1 ? 'Sim' : 'Não';
        // nivel de acesso: 1 = NPI, 2 = Aluno Tutor, 3 = NPI e Aluno Turor, outros = não
        $usuario->nivel_de_acesso = $usuario->nivel_de_acesso == 1 ? 'NPI' : ($usuario->nivel_de_acesso == 2 ? 'Aluno Tutor' : ($usuario->nivel_de_acesso == 3 ? 'NPI e Aluno Tutor' : 'Não'));
        // treinamento concluido: 0 = nenhum, 1 = NPI, 2 = Aluno Tutor, 3 = NPI e Aluno Tutor
        $usuario->treinamento_concluido = $usuario->treinamento_concluido == 1 ? 'NPI' : ($usuario->treinamento_concluido == 2 ? 'Aluno Tutor' : ($usuario->treinamento_concluido == 3 ? 'NPI e Aluno Tutor' : 'Nenhum'));


        return (
            View::make('alunos.show')
            ->with('aluno', $usuario)
        );
    }

    public function atvsUser($id){

        $atv = (DB::table('usuario_atividade')
        //only first 25 characters from atividade.descricao
        ->select(
            DB::raw('SUBSTRING(atividade.descricao, 1, 45) as descricao'),
            DB::raw("DATE_FORMAT(atividade.data_atividade, '%d/%m/%Y') as data_atividade"),
            DB::raw("TIME_FORMAT(atividade.hora_atividade, '%H:%i') as hora_atividade"),
            'atividade.atividade_id') 
        // DB::raw("DATE_FORMAT(atividade.data_atividade, '%d/%m/%Y') as data_atividade"),
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
        //if auth user usuario_id is not equal to $id, redirect to dashboard
        if (Auth::user()->admin != 1) {
            if (Auth::user()->usuario_id != $id) {
                return Redirect::to('dashboard');
            }
        }


        $usuario = usuario::find($id);
        $usuario_curso = usuario_curso::where('usuario_id', $id)->first();
        $curso = curso::find($usuario_curso->curso_id);
        $usuario->curso = $curso->nome;
        $usuario->horario = $usuario_curso->horario;
        $usuario->npi = $usuario->npi == 1 ? 'Sim' : 'Não';
        $usuario->aluno_tutor = $usuario->aluno_tutor == 1 ? 'Sim' : 'Não';
        $usuario->treinamento_concluido = $usuario->treinamento_concluido == 1 ? 'NPI' : ($usuario->treinamento_concluido == 2 ? 'Aluno Tutor' : ($usuario->treinamento_concluido == 3 ? 'NPI e Aluno Tutor' : 'Nenhum'));
        $usuario->nivel_de_acesso = $usuario->nivel_de_acesso == 1 ? 'NPI' : ($usuario->nivel_de_acesso == 2 ? 'Aluno Tutor' : ($usuario->nivel_de_acesso == 3 ? 'NPI e Aluno Tutor' : 'Não'));
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
        if (Auth::user()->admin != 1) {
            if (Auth::user()->usuario_id != $id) {
                return Redirect::to('dashboard');
            }
        }

        $rules = array( 
            'nome' => 'required',
            'email' => 'required|email',
            'telefone' => 'regex:/^\d{2}\s\d{9}$/',
            //check if curso with name exists in the database
            'curso' => 'required|exists:curso,nome',
            'acesso' => 'required|in:0,1,2,3',
            'treinamento_concluido' => 'required|in:0,1,2,3',
            'npi' => 'required|in:0,1',
            'aluno_tutor' => 'required|in:0,1',
            'admin' => 'required|in:0,1',
        );
        $messages = [
            'nome.required' => 'O campo nome é obrigatório',
            'email.required' => 'O campo email é obrigatório',
            'email.email' => 'O campo email deve ser um email válido',
            'telefone.regex' => 'O campo telefone deve ser no formato (xx xxxxxxxxx)',
            'curso.required' => 'O campo curso é obrigatório',
            'curso.exists' => 'O curso informado não existe nos registros',
            'acesso.required' => 'O campo acesso é obrigatório',
            'acesso.in' => 'O campo acesso deve ser sim ou não',
            'treinamento_concluido.required' => 'O campo treinamento concluído é obrigatório',
            'treinamento_concluido.in' => 'O campo treinamento concluído deve ser sim ou não',
            'npi.required' => 'O campo NPI é obrigatório',
            'npi.in' => 'O campo NPI deve ser sim ou não',
            'aluno_tutor.required' => 'O campo aluno tutor é obrigatório',
            'aluno_tutor.in' => 'O campo aluno tutor deve ser sim ou não',
            'admin.required' => 'O campo admin é obrigatório',
            'admin.in' => 'O campo admin deve ser sim ou não',
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
                
                if($usuario->npi != Request::get('npi')){
                    array_push($changedFields[0], 'NPI');
                    // 1 = Sim, 0 = Não
                    array_push($changedFields[1], (Request::get('npi') == 1) ? 'Sim' : 'Não');
                    array_push($changedFields[2], ($usuario->npi == 1) ? 'Sim' : 'Não');
                }
                if($usuario->aluno_tutor != Request::get('aluno_tutor')){
                    array_push($changedFields[0], 'Aluno Tutor');
                    array_push($changedFields[1], (Request::get('aluno_tutor') == 1) ? 'Sim' : 'Não');
                    array_push($changedFields[2], ($usuario->aluno_tutor == 1) ? 'Sim' : 'Não');
                }
                // treinamento concluido
                if($usuario->treinamento_concluido != Request::get('treinamento_concluido')){
                    array_push($changedFields[0], 'Treinamento Concluído');
                    // 1 = NPI, 2 = Aluno Tutor, 3 = NPI e Aluno Tutor, 0 = Não
                    if(Request::get('treinamento_concluido') == 0){
                        array_push($changedFields[1], 'Não');
                    }elseif(Request::get('treinamento_concluido') == 1){
                        array_push($changedFields[1], 'NPI');
                    }elseif(Request::get('treinamento_concluido') == 2){
                        array_push($changedFields[1], 'Aluno Tutor');
                    }elseif(Request::get('treinamento_concluido') == 3){
                        array_push($changedFields[1], 'NPI e Aluno Tutor');
                    }
                    if($usuario->treinamento_concluido == 0){
                        array_push($changedFields[2], 'Não');
                    }elseif($usuario->treinamento_concluido == 1){
                        array_push($changedFields[2], 'NPI');
                    }elseif($usuario->treinamento_concluido == 2){
                        array_push($changedFields[2], 'Aluno Tutor');
                    }elseif($usuario->treinamento_concluido == 3){
                        array_push($changedFields[2], 'NPI e Aluno Tutor');
                    }

                }
                // like above but for nivel de acesso, 0 = Não, 1 = NPI, 2 = Aluno Tutor, 3 = NPI e Aluno Tutor
                if($usuario->nivel_de_acesso != Request::get('acesso')){
                    array_push($changedFields[0], 'Nível de Acesso');

                    if(Request::get('acesso') == 0){
                        array_push($changedFields[1], 'Não');
                    }elseif(Request::get('acesso') == 1){
                        array_push($changedFields[1], 'NPI');
                    }elseif(Request::get('acesso') == 2){
                        array_push($changedFields[1], 'Aluno Tutor');
                    }elseif(Request::get('acesso') == 3){
                        array_push($changedFields[1], 'NPI e Aluno Tutor');
                    }

                    if($usuario->nivel_de_acesso == 0){
                        array_push($changedFields[2], 'Não');
                    }elseif($usuario->nivel_de_acesso == 1){
                        array_push($changedFields[2], 'NPI');
                    }elseif($usuario->nivel_de_acesso == 2){
                        array_push($changedFields[2], 'Aluno Tutor');
                    }elseif($usuario->nivel_de_acesso == 3){
                        array_push($changedFields[2], 'NPI e Aluno Tutor');
                    }
                }
                if($usuario->admin != Request::get('admin')){
                    array_push($changedFields[0], 'Admin');
                    //if admin is 1 = Sim, if 0 = Não
                    if(Request::get('admin') == 1){
                        array_push($changedFields[1], 'Sim');
                    }else{
                        array_push($changedFields[1], 'Não');
                    }
                    //if admin is 1 = Sim, if 0 = Não
                    if($usuario->admin == 1){
                        array_push($changedFields[2], 'Sim');
                    }else{
                        array_push($changedFields[2], 'Não');
                    }
                }


                $usuario->email = Request::get('email');
                $usuario->nome = Request::get('nome');
                $usuario->telefone = Request::get('telefone');
                $usuario->npi = Request::get('npi');
                $usuario->aluno_tutor = Request::get('aluno_tutor');
                $usuario->nivel_de_acesso = Request::get('acesso');
                $usuario->treinamento_concluido = Request::get('treinamento_concluido');
                $usuario->admin = Request::get('admin');
                $usuario->save();
                
                $oldUsuario_curso = usuario_curso::where('usuario_id', $id)->first();
                $oldHor = $oldUsuario_curso->horario;
                $oldCurso = $oldUsuario_curso->curso_id;
                $oldCurNome = curso::where('curso_id', $oldUsuario_curso->curso_id)->first()->nome;
                DB::delete('delete from usuario_curso where usuario_id = ?', [$id]);
                
                if($oldHor != Request::get('horario')){
                    array_push($changedFields[0], 'Horário');
                    array_push($changedFields[1], Request::get('horario'));
                    array_push($changedFields[2], $oldHor);
                }
                $curso = curso::where('nome', Request::get('curso'))->first();
                // dd($usuario_curso,$id, $curso);

                if($oldCurso != $curso->curso_id){
                    array_push($changedFields[0], 'Curso');
                    array_push($changedFields[1], Request::get('curso'));
                    array_push($changedFields[2], $oldCurNome);
                }
                // create new usuario_curso
                $usuario_curso = new usuario_curso;
                $usuario_curso->usuario_id = $usuario->usuario_id;
                $curso = curso::where('nome', Request::get('curso'))->first();
                $usuario_curso->curso_id = $curso->curso_id;
                $usuario_curso->horario = Request::get('horario');
                $usuario_curso->save();
                // $usuario_curso = usuario_curso::where('usuario_id', $id)->get()[0];
                
                
                
                
                
                

                

                
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

    public function updateSelf(Request $request){

        // this function is equal to update, but it is used when the user is editing his own data
        
        $rules = array( 
            'nome' => 'required',
            'email' => 'required|email',
            'telefone' => 'regex:/^\d{2}\s\d{9}$/',
            //check if curso with name exists in the database
            'curso' => 'required|exists:curso,nome',
            
            
            
        );
        $messages = [
            'nome.required' => 'O campo nome é obrigatório',
            'email.required' => 'O campo email é obrigatório',
            'email.email' => 'O campo email deve ser um email válido',
            'telefone.regex' => 'O campo telefone deve ser no formato (xx xxxxxxxxx)',
            'curso.required' => 'O campo curso é obrigatório',
            'curso.exists' => 'O curso informado não existe nos registros',
            'acesso.required' => 'O campo acesso é obrigatório',
            'acesso.in' => 'O campo acesso deve ser sim ou não',
            'treinamento_concluido.required' => 'O campo treinamento concluído é obrigatório',
            'treinamento_concluido.in' => 'O campo treinamento concluído deve ser sim ou não',
            'npi.required' => 'O campo NPI é obrigatório',
            'npi.in' => 'O campo NPI deve ser sim ou não',
            'aluno_tutor.required' => 'O campo aluno tutor é obrigatório',
            'aluno_tutor.in' => 'O campo aluno tutor deve ser sim ou não',
        ];

        $validator = Validator::make(Request::all(), $rules, $messages);
        $id = Auth::user()->usuario_id;

        if ($validator->fails()) {
            return Redirect::to('alunos/' . $id . '/selfEdit')
                ->withErrors($validator)
                ->withInput(Request::except('password'));
        } else {
            $result = DB::transaction(function () use ($id) {
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
                // if($usuario->nivel_de_acesso != Request::get('acesso')){
                //     array_push($changedFields[0], 'Nível de acesso');
                //     array_push($changedFields[1], Request::get('acesso'));
                //     array_push($changedFields[2], $usuario->nivel_de_acesso);
                // }
                //npi
                if($usuario->npi != Request::get('npi')){
                    array_push($changedFields[0], 'NPI');
                    array_push($changedFields[1], Request::get('npi'));
                    array_push($changedFields[2], $usuario->npi);
                }
                //aluno tutor
                if($usuario->aluno_tutor != Request::get('aluno_tutor')){
                    array_push($changedFields[0], 'Aluno Tutor');
                    array_push($changedFields[1], Request::get('aluno_tutor'));
                    array_push($changedFields[2], $usuario->aluno_tutor);
                }

                $usuario->email = Request::get('email');
                $usuario->nome = Request::get('nome');
                $usuario->telefone = Request::get('telefone');
                $usuario->password = Request::get('password');
                $usuario->save();

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

                $historico_controller = new historicoController();
                $historico_controller->store([implode(", ", $changedFields[0]), "editar", $usuario->usuario_id, Auth::user()->usuario_id, implode(", ", $changedFields[2]), implode(", ", $changedFields[1]),1]);

                return ('alunos/' . $usuario->usuario_id . '/selfShow');
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
        $data = array_map(null, $data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6]);

        
        $rules = array(
            '*.0' => 'required|string|max:255',
            '*.1' => 'required|string|max:255|exists:curso,nome',
            '*.2' => 'nullable|string|max:255|in:Manhã,Tarde,Noite',
            '*.3' => 'required|email|max:255',
            '*.4' => 'nullable|regex:/^\d{2}\s\d{9}$/',
            '*.5' => 'nullable|in:Sim,Não',
            '*.6' => 'nullable|in:Sim,Não',
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
                //converti key[5] e key[6] para 1 (sim) ou 0 (não)
                foreach($data as $key => $value){
                    if($value[5] == 'Sim'){
                        $data[$key][5] = 1;
                    }else{
                        $data[$key][5] = 0;
                    }
                    if($value[6] == 'Sim'){
                        $data[$key][6] = 1;
                    }else{
                        $data[$key][6] = 0;
                    }
                }
                foreach ($data as &$key) {
                    //cria aluno
                    $usuario = new usuario;
                    $usuario->email = $key[3];
                    $usuario->nome = $key[0];
                    $usuario->telefone = $key[4];
                    $usuario->npi = $key[5];
                    $usuario->aluno_tutor = $key[6];
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

    //login
    public function login(Request $request){
        $data = Request::all();
        $rules = array(
            'email' => 'required|email|max:255',
            'password' => 'required|string|max:255',
        );
        $messages = array(
            'required' => 'O campo :attribute é obrigatório.',
            'string' => 'O campo :attribute deve ser uma string.',
            'max' => 'O campo :attribute deve ter no máximo :max caracteres.',
            'email' => 'O campo :attribute deve ser um email válido.',
        );
        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            return Redirect::to('/')
            ->withErrors($validator)
            ->withInput();
        }else{
            $user = usuario::where([
                'email' => Request::get('email'), 
                'password' => Request::get('password')
            ])->first();

            if ($user) {
                //if password is empty, return error
                if($user->password == ""){
                    return Redirect::to('/')
                    ->withErrors(['email' => 'Senha não cadastrada, Fáça login por google e defina uma senha.'])
                    ->withInput();
                }else{
                    Auth::login($user);
                    return redirect()->intended('dashboard');
                }
            }else{
                return Redirect::to('/')
                ->withErrors(['email' => 'Email ou senha incorretos.'])
                ->withInput();
            }
        }
    }
}
