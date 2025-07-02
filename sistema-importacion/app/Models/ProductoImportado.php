<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoImportado extends Model
{
    use HasFactory;

    protected $table = 'productos_importados';

    protected $fillable = [
        'nombre',
        'descripcion',
        'codigo_arancelario',
        'precio_fob',
        'unidad_medida'
    ];

    protected $casts = [
        'precio_fob' => 'decimal:2'
    ];

    public function ordenesImportacion()
    {
        return $this->belongsToMany(OrdenImportacion::class, 'orden_producto')
                    ->withPivot('cantidad', 'precio_unitario')
                    ->withTimestamps();
    }

    public function contenedores()
    {
        return $this->belongsToMany(Contenedor::class, 'contenedor_producto')
                    ->withPivot('cantidad')
                    ->withTimestamps();
    }
}