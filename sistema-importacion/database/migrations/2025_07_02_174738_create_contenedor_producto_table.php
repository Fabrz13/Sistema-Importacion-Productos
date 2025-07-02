<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContenedorProductoTable extends Migration
{
    public function up()
    {
        Schema::create('contenedor_producto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contenedor_id')->constrained('contenedores')->onDelete('cascade');
            $table->foreignId('producto_importado_id')->constrained('productos_importados')->onDelete('cascade');
            $table->integer('cantidad');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contenedor_producto');
    }
}