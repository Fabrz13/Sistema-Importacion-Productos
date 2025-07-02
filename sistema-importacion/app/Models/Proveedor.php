<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';

    protected $fillable = [
        'nombre',
        'pais',
        'email',
        'telefono',
        'direccion'
    ];

    public function ordenesImportacion()
    {
        return $this->hasMany(OrdenImportacion::class);
    }
}