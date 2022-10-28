<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class historicoCurso extends Model
{
    use HasFactory;
    protected $table = 'historicoCurso';
    protected $primaryKey = 'historico_id';
    public $timestamps = false;
}
