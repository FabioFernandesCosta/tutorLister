<?php

namespace App\Http\Controllers;
use Request;
use Redirect;
use App\Models\requisitante;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class requisitanteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getdata(){
        $requisitantes = requisitante::all();
        return datatables()->of($requisitantes)->toJson();

    }

    public function index()
    {
        //
        return view('requisitantes.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('requisitantes.create');
    }

    public function consultar(Request $request){

        $search = str_replace(['.', '#'], '' , explode('_', Request::get('term')));
        // $result = DB::table('requisitante')->where('empresa', 'LIKE', '%'. $search[1]. '%')->pluck('empresa');
        // same as above buut with distinct
        $result = DB::table('requisitante')->where('empresa', 'LIKE', '%'. $search[1]. '%')->distinct()->pluck('empresa');
 
        return response()->json($result);
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
            'empresa' => 'required',
            'nome' => 'required',
            'email' => 'required',
            'telefone' => 'required',
        );

        $messages = array(
            'empresa.required' => 'O campo empresa é obrigatório',
            'nome.required' => 'O campo nome é obrigatório',
            'email.required' => 'O campo email é obrigatório',
            'telefone.required' => 'O campo telefone é obrigatório',
        );

        $validator = Validator::make(Request::all(), $rules, $messages);

        if ($validator->fails()) {
            return Redirect::to('requisitantes/create')
                        ->withErrors($validator)
                        ->withInput();
        } else {

            $requisitante = new requisitante;
            $requisitante->empresa = Request::get('empresa');
            $requisitante->nome = Request::get('nome');
            $requisitante->email = Request::get('email');
            $requisitante->telefone = Request::get('telefone');
            $requisitante->save();

            $user = Auth::user();
            $user_id = $user->usuario_id;
            $historico_controller = new historicoController;
            $historico_controller->store(["", "Requisitante craido", $requisitante->requisitante_id, $user_id, NULL, NULL, 3]);


            // redirect
            return Redirect::to('requisitantes/'.$requisitante->id)->with('message', 'Requisitante cadastrado com sucesso!');
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
        $requisitante = requisitante::find($id);
        return (View::make('requisitantes.show')->with('requisitante', $requisitante));
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
        $requisitante = requisitante::find($id);
        return (View::make('requisitantes.edit')->with('requisitante', $requisitante));
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
            'empresa' => 'required',
            'email' => 'required',
            'telefone' => 'required',
        );

        $messages = array(
            'nome.required' => 'O campo nome é obrigatório',
            'empresa.required' => 'O campo empresa é obrigatório',
            'email.required' => 'O campo email é obrigatório',
            'telefone.required' => 'O campo telefone é obrigatório',
        );

        $validator = Validator::make(Request::all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } else {

            DB::transaction(function () use ($id) {
                $requisitante = requisitante::find($id);
                $changedFields = array(array(), array(), array());
                if($requisitante->nome != Request::get('nome')){
                    array_push($changedFields[0], 'Nome');
                    array_push($changedFields[1], Request::get('nome'));
                    array_push($changedFields[2], $requisitante->nome);
                }
                if($requisitante->empresa != Request::get('empresa')){
                    array_push($changedFields[0], 'Empresa');
                    array_push($changedFields[1], Request::get('empresa'));
                    array_push($changedFields[2], $requisitante->empresa);
                }
                if($requisitante->email != Request::get('email')){
                    array_push($changedFields[0], 'Email');
                    array_push($changedFields[1], Request::get('email'));
                    array_push($changedFields[2], $requisitante->email);
                }
                if($requisitante->telefone != Request::get('telefone')){
                    array_push($changedFields[0], 'Telefone');
                    array_push($changedFields[1], Request::get('telefone'));
                    array_push($changedFields[2], $requisitante->telefone);
                }


                $requisitante->nome = Request::get('nome');
                $requisitante->empresa = Request::get('empresa');
                $requisitante->email = Request::get('email');
                $requisitante->telefone = Request::get('telefone');
                $requisitante->save();

                $user = Auth::user();
                $user_id = $user->usuario_id;
                $historico_controller = new historicoController;
                $historico_controller->store([implode(", ", $changedFields[0]), "editar", $requisitante->requisitante_id, $user_id, implode(", ", $changedFields[2]), implode(", ", $changedFields[1]),3]);

            });
            return Redirect::to('requisitantes/'.$id)->with('message', 'Requisitante atualizado com sucesso!');
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
