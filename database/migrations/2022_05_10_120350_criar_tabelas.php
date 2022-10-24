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
            $table->string('organizacao');
            $table->string('telefone');
            $table->boolean('ativo');
            $table->boolean('npi');
            $table->boolean('aluno_tutor');
            $table->boolean('treinamento_concluido');
            $table->integer('nivel_de_acesso');
            $table->string('email')->unique();
            //provider_id
            $table->string('provider_id')->nullable();
            //avatar
            $table->string('avatar')->nullable();
            //admin
            $table->boolean('admin')->default(false);

            $table->rememberToken();
        });

        Schema::create('atividade', function (Blueprint $table) {
            $table->id('atividade_id');
            $table->date('data_atividade');
            $table->date('data_registro');
            $table->time('hora_atividade');
            $table->time('hora_registro');
            $table->time('carga');
            $table->text('descricao');
            $table->text('status');
            //organizacao npi ou aluno_tutor
            $table->text('organizacao');
        });
        

        Schema::create('usuario_atividade', function (Blueprint $table) {
            $table->bigInteger('usuario_id') -> unsigned();
            $table->bigInteger('atividade_id') -> unsigned();
        });

        Schema::table('usuario_atividade', function($table){
            $table->foreign('usuario_id') -> references('usuario_id') -> on('usuario') -> onDelete('cascade');
            $table->foreign('atividade_id') -> references('atividade_id') -> on('atividade') -> onDelete('cascade');
        });

        

        Schema::create('requisitante', function (Blueprint $table) {
            $table->id('requisitante_id');
            $table->string('nome');
            $table->string('empresa');
            $table->string('telefone')->nullable();
            $table->string('email')->unique()->nullable();
        });


        Schema::create('atividade_requisitante', function (Blueprint $table) {
            $table->bigInteger('requisitante_id') -> unsigned();
            $table->bigInteger('atividade_id') -> unsigned();
        });

        Schema::table('atividade_requisitante', function($table){
            $table->foreign('requisitante_id') -> references('requisitante_id') -> on('requisitante') -> onDelete('cascade');
            $table->foreign('atividade_id') -> references('atividade_id') -> on('atividade') -> onDelete('cascade');
        });

        /*
        
        Schema::create('atividade_requisitante', function (Blueprint $table) {
            $table->foreign('requisitante_id') -> references('requisitante_id') -> on('requisitante') -> onUpdate('cascade') -> onDelete('cascade');
            $table->foreign('atividade_id') -> references('atividade_id') -> on('atividade') -> onUpdate('cascade') -> onDelete('cascade');
        });

        */
        


        Schema::create('horario', function (Blueprint $table) {
            $table->id('horario_id');
            $table->date('dia');
            $table->bigInteger('usuario_id') -> unsigned();
            $table->foreign('usuario_id') -> references('usuario_id') -> on('usuario') -> onUpdate('cascade') -> onDelete('cascade');
            $table->time('hora_inicio');
            $table->time('hora_fim')->nullable();
        });

        Schema::create('curso', function (Blueprint $table) {
            $table->id('curso_id');
            $table->string('nome');
            $table->string('area_curso')->nullable();
        });


        Schema::create('usuario_curso', function (Blueprint $table) {
            $table->bigInteger('curso_id') -> unsigned();
            $table->bigInteger('usuario_id') -> unsigned();
            $table->string('horario');
        });

        Schema::table('usuario_curso', function($table){
            $table->foreign('curso_id') -> references('curso_id') -> on('curso') -> onDelete('cascade');
            $table->foreign('usuario_id') -> references('usuario_id') -> on('usuario') -> onDelete('cascade');
        });

        /*
        Schema::create('usuario_curso', function (Blueprint $table) {
            $table->foreign('curso_id') -> references('curso_id') -> on('curso') -> onUpdate('cascade') -> onDelete('cascade');
            $table->foreign('usuario_id') -> references('usuario_id') -> on('usuario') -> onUpdate('cascade') -> onDelete('cascade');
        });
        */

        Schema::create('historico', function (Blueprint $table){
            $table->id('historico_id');
            $table->string('campo_modificado');
            $table->string('acao');
            $table->bigInteger('atividade_id') -> unsigned();
            $table->bigInteger('usuario_id') -> unsigned();
            $table->foreign('atividade_id') -> references('atividade_id') -> on('atividade') -> onUpdate('cascade') -> onDelete('cascade');
            $table->foreign('usuario_id') -> references('usuario_id') -> on('usuario') -> onUpdate('cascade') -> onDelete('cascade');
            $table->date('data_modificacao');
            $table->string('valor_anterior');
            $table->string('novo_valor');
        });
        
        Schema::create('historicoUser', function (Blueprint $table){
            $table->id('historico_id');
            $table->string('campo_modificado');
            $table->string('acao');
            $table->bigInteger('usuario_id') -> unsigned();
            $table->foreign('usuario_id') -> references('usuario_id') -> on('usuario') -> onUpdate('cascade') -> onDelete('cascade');
            $table->bigInteger('editor') -> unsigned();
            $table->foreign('editor') -> references('usuario_id') -> on('usuario') -> onUpdate('cascade') -> onDelete('cascade');
            $table->date('data_modificacao');
            $table->string('valor_anterior')->nullable();
            $table->string('novo_valor')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::drop('usuario_atividade');
        Schema::drop('atividade_requisitante');
        Schema::drop('usuario_curso');
        Schema::drop('atividade');
        Schema::drop('requisitante');
        Schema::drop('curso');
        Schema::drop('horario');
        Schema::drop('historico');
        Schema::drop('usuario');
        Schema::enableForeignKeyConstraints();
    }
};
