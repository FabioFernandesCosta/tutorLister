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
        Schema::create('historicoCurso', function (Blueprint $table){
            $table->id('historico_id');
            $table->string('campo_modificado');
            $table->string('acao');
            $table->bigInteger('curso_id') -> unsigned();
            $table->foreign('curso_id') -> references('curso_id') -> on('curso') -> onUpdate('cascade') -> onDelete('cascade');
            $table->bigInteger('editor') -> unsigned();
            $table->foreign('editor') -> references('usuario_id') -> on('usuario') -> onUpdate('cascade') -> onDelete('cascade');
            $table->date('data_modificacao');
            $table->string('valor_anterior')->nullable();
            $table->string('novo_valor')->nullable();
        });
        
        Schema::create('historicoRequisitante', function (Blueprint $table){
            $table->id('historico_id');
            $table->string('campo_modificado');
            $table->string('acao');
            $table->bigInteger('requisitante_id') -> unsigned();
            $table->foreign('requisitante_id') -> references('requisitante_id') -> on('requisitante') -> onUpdate('cascade') -> onDelete('cascade');
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
        Schema::dropIfExists('historicoCurso');
        Schema::dropIfExists('historicoRequisitante');
    }
};
