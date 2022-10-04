<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class horario extends Model
{
    use HasFactory;
    protected $table = 'horario';
    protected $primaryKey = 'horario_id';
    public $timestamps = false;
}
