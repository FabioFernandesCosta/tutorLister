<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class usuario_curso extends Model
{
    use HasFactory;
    protected $table = 'usuario_curso';
    protected $primaryKey = null;
    public $timestamps = false;
    public $incrementing = false;
}
