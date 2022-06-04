<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class requisitante extends Model
{
    use HasFactory;
    protected $table = 'requisitante';
    protected $primaryKey = 'requisitante_id';
    public $timestamps = false;
}
