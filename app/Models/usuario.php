<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;   

class usuario extends Authenticatable
{
    use HasFactory;
    protected $table = 'usuario';
    protected $primaryKey = 'usuario_id';
    protected $fillable = [
        'nome',
        'email',
        'password',
        'avatar',
        'provider_id',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    public $timestamps = false;



    public function isAdmin(){
        return $this->admin==1;
    }
}

