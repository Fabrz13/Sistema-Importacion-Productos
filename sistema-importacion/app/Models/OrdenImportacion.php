<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenImportacion extends Model
{
    use HasFactory;

    protected $table = 'ordenes_importacion';

    protected $fillable = [
        'codigo_orden',
        'fecha_emision',
        'proveedor_id',
        'estado',
        'valor_total'
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'valor_total' => 'decimal:2'
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function productos()
    {
        return $this->belongsToMany(ProductoImportado::class, 'orden_producto')
                    ->withPivot('cantidad', 'precio_unitario')
                    ->withTimestamps();
    }

    // Generar código de orden automáticamente
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->codigo_orden)) {
                $model->codigo_orden = 'IMP-' . date('Y') . '-' . str_pad(
                    self::whereYear('created_at', date('Y'))->count() + 1, 
                    4, 
                    '0', 
                    STR_PAD_LEFT
                );
            }
        });
    }
}