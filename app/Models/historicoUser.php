<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class historicoUser extends Model
{
    use HasFactory;
    protected $table = 'historicoUser';
    protected $primaryKey = 'historico_id';
    public $timestamps = false;
}

