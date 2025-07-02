<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdenesImportacionTable extends Migration
{
    public function up()
    {
        Schema::create('ordenes_importacion', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_orden')->unique();
            $table->date('fecha_emision');
            $table->foreignId('proveedor_id')->constrained('proveedores')->onDelete('cascade');
            $table->enum('estado', ['pendiente', 'embarcado', 'recibido'])->default('pendiente');
            $table->decimal('valor_total', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ordenes_importacion');
    }
}