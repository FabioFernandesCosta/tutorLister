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
            $table->integer('telefone');
            $table->boolean('ativo');
            $table->integer('nivel_de_acesso');
            $table->string('email')->unique();
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
            $table->time('email');
        });

        Schema::create('curso', function (Blueprint $table) {
            $table->id('curso_id');
            $table->string('nome_curso');
            $table->string('area_curso');
        });


        Schema::create('usuario_curso', function (Blueprint $table) {
            $table->bigInteger('curso_id') -> unsigned();
            $table->bigInteger('usuario_id') -> unsigned();
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
