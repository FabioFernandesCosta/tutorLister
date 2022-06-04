<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\requisitante;
use View;

class RequisitanteController extends Controller
{
    public static function consultar($id){
        $req = requisitante::find($id);

        return $req;
    }
}
