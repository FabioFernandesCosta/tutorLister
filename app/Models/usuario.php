<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class usuario extends Model
{
    use HasFactory;
    protected $table = 'usuario';
    protected $primaryKey = 'usuario_id';
    public $timestamps = false;
    protected $hidden = ['senha'];
}
