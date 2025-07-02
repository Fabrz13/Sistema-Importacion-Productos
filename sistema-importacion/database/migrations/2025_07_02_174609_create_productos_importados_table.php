<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductosImportadosTable extends Migration
{
    public function up()
    {
        Schema::create('productos_importados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion');
            $table->string('codigo_arancelario');
            $table->decimal('precio_fob', 10, 2);
            $table->string('unidad_medida')->default('unidad');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('productos_importados');
    }
}