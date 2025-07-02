<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contenedor extends Model
{
    use HasFactory;

    protected $table = 'contenedores';

    protected $fillable = [
        'numero_contenedor',
        'tipo',
        'fecha_estimada_llegada',
        'estado'
    ];

    protected $casts = [
        'fecha_estimada_llegada' => 'date'
    ];

    public function productos()
    {
        return $this->belongsToMany(ProductoImportado::class, 'contenedor_producto')
                    ->withPivot('cantidad')
                    ->withTimestamps();
    }
}