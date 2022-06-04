<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class atividade_requisitante extends Model
{
    use HasFactory;
    protected $table = 'atividade_requisitante';
    protected $primaryKey = null;
    public $timestamps = false;
    public $incrementing = false;
}
