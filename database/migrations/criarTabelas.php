<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;




return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->id('usuario_id');
            $table->string('nome');
            $table->string('senha');
            $table->string('organizacao');
            $table->int('telefone');
            $table->boolean('ativo');
            $table->int('nivel_de_acesso');
            $table->string('email')->unique();
            $table->rememberToken();
        });

        Schema::create('atividade', function (Blueprint $table) {
            $table->id('atvID');
            $table->date('data_atividade');
            $table->date('data_registro');
            $table->time('hora_atividade');
            $table->time('hora_registro');
            $table->time('carga');
            $table->string('descricao');
        });
        

        Schema::create('usuarioXAtividade', function (Blueprint $table) {
            $table->foreign('usuario_id') -> references('usuario_id') -> on('usuario') -> onUpdate('cascade') -> onDelete('cascade');
            $table->foreign('atividade_id') -> references('atividade_id') -> on('atividade') -> onUpdate('cascade') -> onDelete('cascade');
        });

        

        Schema::create('requisitante', function (Blueprint $table) {
            $table->id('requisitante_id');
            $table->string('nome');
            $table->string('empresa');
            $table->int('telefone');
            $table->string('email')->unique();
        });


        Schema::create('atividadeXRequisitante', function (Blueprint $table) {
            $table->foreign('requisitante_id') -> references('requisitante_id') -> on('requisitante') -> onUpdate('cascade') -> onDelete('cascade');
            $table->foreign('atividade_id') -> references('atividade_id') -> on('atividade') -> onUpdate('cascade') -> onDelete('cascade');
        });
        


        Schema::create('horario', function (Blueprint $table) {
            $table->id('horario_id');
            $table->date('dia');
            $table->foreign('usuario_id') -> references('usuario_id') -> on('usuario') -> onUpdate('cascade') -> onDelete('cascade');
            $table->time('hora_inicio');
            $table->time('email');
        });

        Schema::create('curso', function (Blueprint $table) {
            $table->id('curso_id');
            $table->string('nome_curso');
            $table->string('area_curso');
        });

        Schema::create('usuarioXCurso', function (Blueprint $table) {
            $table->foreign('curso_id') -> references('curso_id') -> on('curso') -> onUpdate('cascade') -> onDelete('cascade');
            $table->foreign('usuario_id') -> references('usuario_id') -> on('usuario') -> onUpdate('cascade') -> onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuarios');
        Schema::dropIfExists('atividade');
        Schema::dropIfExists('usuarioXAtividade');
        Schema::dropIfExists('requisitante');
        Schema::dropIfExists('atividadeXRequisitante');
        Schema::dropIfExists('curso');
        Schema::dropIfExists('usuarioXCurso');
    }
};
