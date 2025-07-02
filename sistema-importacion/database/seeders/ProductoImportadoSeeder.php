<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductoImportado;

class ProductoImportadoSeeder extends Seeder
{
    public function run()
    {
        $productos = [
            [
                'nombre' => 'Laptop Gaming ROG',
                'descripcion' => 'Laptop para gaming de alta gama con procesador Intel i7',
                'codigo_arancelario' => '8471.30.01.00',
                'precio_fob' => 899.99,
                'unidad_medida' => 'unidad'
            ],
            [
                'nombre' => 'Smartphone Android',
                'descripcion' => 'Teléfono inteligente Android con 128GB de almacenamiento',
                'codigo_arancelario' => '8517.12.00.00',
                'precio_fob' => 299.99,
                'unidad_medida' => 'unidad'
            ],
            [
                'nombre' => 'Auriculares Bluetooth',
                'descripcion' => 'Auriculares inalámbricos con cancelación de ruido',
                'codigo_arancelario' => '8518.30.00.00',
                'precio_fob' => 89.99,
                'unidad_medida' => 'unidad'
            ],
            [
                'nombre' => 'Monitor 4K 27"',
                'descripcion' => 'Monitor LED 4K de 27 pulgadas para oficina',
                'codigo_arancelario' => '8528.72.00.00',
                'precio_fob' => 329.99,
                'unidad_medida' => 'unidad'
            ],
            [
                'nombre' => 'Teclado Mecánico RGB',
                'descripcion' => 'Teclado mecánico para gaming con iluminación RGB',
                'codigo_arancelario' => '8471.60.00.00',
                'precio_fob' => 129.99,
                'unidad_medida' => 'unidad'
            ]
        ];

        foreach ($productos as $producto) {
            ProductoImportado::create($producto);
        }
    }
}
