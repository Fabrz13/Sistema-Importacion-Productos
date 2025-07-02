<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContenedoresTable extends Migration
{
    public function up()
    {
        Schema::create('contenedores', function (Blueprint $table) {
            $table->id();
            $table->string('numero_contenedor')->unique();
            $table->enum('tipo', ['20ft', '40ft', 'reefer', '45ft']);
            $table->date('fecha_estimada_llegada');
            $table->enum('estado', ['en_transito', 'llegado', 'descargado'])->default('en_transito');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contenedores');
    }
}