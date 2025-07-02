<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Proveedor;

class ProveedorSeeder extends Seeder
{
    public function run()
    {
        $proveedores = [
            [
                'nombre' => 'Importaciones China S.A.',
                'pais' => 'China',
                'email' => 'ventas@chinaimport.com',
                'telefono' => '+86-138-0013-8000',
                'direccion' => 'Shenzhen, Guangdong, China'
            ],
            [
                'nombre' => 'Global Trade USA',
                'pais' => 'Estados Unidos',
                'email' => 'sales@globaltrade.com',
                'telefono' => '+1-555-123-4567',
                'direccion' => 'Miami, FL, USA'
            ],
            [
                'nombre' => 'European Suppliers Ltd',
                'pais' => 'Alemania',
                'email' => 'info@eurosuppliers.de',
                'telefono' => '+49-30-12345678',
                'direccion' => 'Berlin, Germany'
            ]
        ];

        foreach ($proveedores as $proveedor) {
            Proveedor::create($proveedor);
        }
    }
}