<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class historico extends Model
{
    use HasFactory;
    protected $table = 'historico';
    protected $primaryKey = 'historico_id';
    public $timestamps = false;
}
