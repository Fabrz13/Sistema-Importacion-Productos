<?php

// URL del WSDL
$wsdl = 'http://localhost:8000/soap/importacion?wsdl';

try {
    // Crear cliente SOAP
    $client = new SoapClient($wsdl, [
        'trace' => 1, // Para depuración
        'exceptions' => true
    ]);

    // Ejemplo 1: Crear una orden de importación
    $result = $client->crearOrdenImportacion(
        '2024-03-20', // fecha_emision
        1,            // proveedor_id
        [
            [
                'producto_id' => 1,
                'cantidad' => 5,
                'precio_unitario' => 899.99
            ],
            [
                'producto_id' => 2,
                'cantidad' => 10,
                'precio_unitario' => 299.99
            ]
        ]
    );

    echo "Resultado creación orden: " . $result . "\n\n";

    // Ejemplo 2: Consultar estado de contenedor
    $estadoContenedor = $client->consultarEstadoContenedor('CONT-2024-001');
    echo "Estado del contenedor:\n";
    print_r($estadoContenedor);

} catch (SoapFault $e) {
    echo "Error SOAP: " . $e->getMessage();
}