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
        Schema::dropIfExists('historicoUser');
    }
};
