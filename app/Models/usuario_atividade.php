<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class usuario_atividade extends Model
{
    use HasFactory;
    protected $table = 'usuario_atividade';
    protected $primaryKey = null;
    public $timestamps = false;
    public $incrementing = false;
    
}
