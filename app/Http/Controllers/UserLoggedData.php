<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\usuario;
class UserLoggedData extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getData(Request $request)
    {
        $user = usuario::find(auth()->user()->usuario_id);
        
        return response()->json([
            'avatar' => $user->avatar,
            'name' => $user->nome,
            'id' => $user->usuario_id,
        ]);

    }
}
