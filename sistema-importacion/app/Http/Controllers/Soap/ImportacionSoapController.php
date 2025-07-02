<?php
namespace App\Http\Controllers\Soap;

use App\Http\Controllers\Controller;
use App\Models\OrdenImportacion;
use App\Models\Contenedor;
use App\Models\Proveedor;
use App\Models\ProductoImportado;
use Illuminate\Support\Facades\DB;
use SoapServer;
use SoapFault;

class ImportacionSoapController extends Controller
{
    public function handle()
    {
        $server = new SoapServer(null, [
            'uri' => 'http://localhost:8000/soap/importacion',
            'location' => 'http://localhost:8000/soap/importacion'
        ]);

        $server->setClass(ImportacionSoapService::class);
        $server->handle();
    }

    public function wsdl()
    {
        $wsdl = '<?xml version="1.0" encoding="UTF-8"?>
<definitions xmlns="http://schemas.xmlsoap.org/wsdl/"
             xmlns:tns="http://localhost:8000/soap/importacion"
             xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/"
             xmlns:xsd="http://www.w3.org/2001/XMLSchema"
             targetNamespace="http://localhost:8000/soap/importacion">

  <types>
    <xsd:schema targetNamespace="http://localhost:8000/soap/importacion">
      <xsd:complexType name="OrdenImportacion">
        <xsd:sequence>
          <xsd:element name="fecha_emision" type="xsd:date"/>
          <xsd:element name="proveedor_id" type="xsd:int"/>
          <xsd:element name="productos" type="tns:ArrayOfProducto"/>
        </xsd:sequence>
      </xsd:complexType>
      
      <xsd:complexType name="Producto">
        <xsd:sequence>
          <xsd:element name="producto_id" type="xsd:int"/>
          <xsd:element name="cantidad" type="xsd:int"/>
          <xsd:element name="precio_unitario" type="xsd:decimal"/>
        </xsd:sequence>
      </xsd:complexType>
      
      <xsd:complexType name="ArrayOfProducto">
        <xsd:sequence>
          <xsd:element name="producto" type="tns:Producto" minOccurs="0" maxOccurs="unbounded"/>
        </xsd:sequence>
      </xsd:complexType>
      
      <xsd:complexType name="EstadoContenedor">
        <xsd:sequence>
          <xsd:element name="numero_contenedor" type="xsd:string"/>
          <xsd:element name="estado" type="xsd:string"/>
          <xsd:element name="fecha_estimada_llegada" type="xsd:date"/>
          <xsd:element name="tipo" type="xsd:string"/>
        </xsd:sequence>
      </xsd:complexType>
    </xsd:schema>
  </types>

  <message name="crearOrdenImportacionRequest">
    <part name="orden" type="tns:OrdenImportacion"/>
  </message>
  <message name="crearOrdenImportacionResponse">
    <part name="return" type="xsd:string"/>
  </message>

  <message name="consultarEstadoContenedorRequest">
    <part name="numero_contenedor" type="xsd:string"/>
  </message>
  <message name="consultarEstadoContenedorResponse">
    <part name="return" type="tns:EstadoContenedor"/>
  </message>

  <portType name="ImportacionServicePortType">
    <operation name="crearOrdenImportacion">
      <input message="tns:crearOrdenImportacionRequest"/>
      <output message="tns:crearOrdenImportacionResponse"/>
    </operation>
    <operation name="consultarEstadoContenedor">
      <input message="tns:consultarEstadoContenedorRequest"/>
      <output message="tns:consultarEstadoContenedorResponse"/>
    </operation>
  </portType>

  <binding name="ImportacionServiceBinding" type="tns:ImportacionServicePortType">
    <soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>
    <operation name="crearOrdenImportacion">
      <soap:operation soapAction="crearOrdenImportacion"/>
      <input><soap:body use="encoded" namespace="http://localhost:8000/soap/importacion" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input>
      <output><soap:body use="encoded" namespace="http://localhost:8000/soap/importacion" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output>
    </operation>
    <operation name="consultarEstadoContenedor">
      <soap:operation soapAction="consultarEstadoContenedor"/>
      <input><soap:body use="encoded" namespace="http://localhost:8000/soap/importacion" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></input>
      <output><soap:body use="encoded" namespace="http://localhost:8000/soap/importacion" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/></output>
    </operation>
  </binding>

  <service name="ImportacionService">
    <port name="ImportacionServicePort" binding="tns:ImportacionServiceBinding">
      <soap:address location="http://localhost:8000/soap/importacion"/>
    </port>
  </service>
</definitions>';

        return response($wsdl)->header('Content-Type', 'text/xml');
    }
}

class ImportacionSoapService
{
    /**
     * Crear una orden de importación
     */
    public function crearOrdenImportacion($fecha_emision, $proveedor_id, $productos)
    {
        try {
            // Registrar los datos recibidos para depuración
            \Log::info('Datos recibidos en SOAP:', [
                'fecha_emision' => $fecha_emision,
                'proveedor_id' => $proveedor_id,
                'productos' => $productos
            ]);

            // Convertir los productos a array si es necesario
            $productosArray = [];
            if (is_object($productos)) {
                // Si es un solo producto
                if (isset($productos->producto_id)) {
                    $productosArray[] = [
                        'producto_id' => $productos->producto_id,
                        'cantidad' => $productos->cantidad,
                        'precio_unitario' => $productos->precio_unitario
                    ];
                }
                // Si es un array de productos
                elseif (isset($productos->item)) {
                    foreach ($productos->item as $item) {
                        $productosArray[] = [
                            'producto_id' => $item->producto_id,
                            'cantidad' => $item->cantidad,
                            'precio_unitario' => $item->precio_unitario
                        ];
                    }
                }
            } elseif (is_array($productos)) {
                $productosArray = $productos;
            }

            // Validar que tenemos productos
            if (empty($productosArray)) {
                throw new SoapFault("CLIENT", "No se proporcionaron productos válidos");
            }

            // Resto del código de creación de la orden...
            $proveedor = Proveedor::find($proveedor_id);
            if (!$proveedor) {
                throw new SoapFault("CLIENT", "Proveedor no encontrado");
            }

            DB::beginTransaction();

            $orden = OrdenImportacion::create([
                'fecha_emision' => $fecha_emision,
                'proveedor_id' => $proveedor_id,
                'estado' => 'pendiente'
            ]);

            $valorTotal = 0;
            
            foreach ($productosArray as $productoData) {
                $producto = ProductoImportado::find($productoData['producto_id']);
                if (!$producto) {
                    throw new SoapFault("CLIENT", "Producto con ID {$productoData['producto_id']} no encontrado");
                }

                $subtotal = $productoData['cantidad'] * $productoData['precio_unitario'];
                $valorTotal += $subtotal;

                $orden->productos()->attach($productoData['producto_id'], [
                    'cantidad' => $productoData['cantidad'],
                    'precio_unitario' => $productoData['precio_unitario']
                ]);
            }

            $orden->update(['valor_total' => $valorTotal]);

            DB::commit();

            return "Orden de importación creada exitosamente. Código: " . $orden->codigo_orden;

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error en SOAP: ' . $e->getMessage());
            throw new SoapFault("SERVER", "Error al crear la orden: " . $e->getMessage());
        }
    }

    /**
     * Consultar estado de un contenedor
     */
    public function consultarEstadoContenedor($numero_contenedor)
    {
        try {
            $contenedor = Contenedor::where('numero_contenedor', $numero_contenedor)->first();

            if (!$contenedor) {
                throw new SoapFault("CLIENT", "Contenedor no encontrado");
            }

            return [
                'numero_contenedor' => $contenedor->numero_contenedor,
                'estado' => $contenedor->estado,
                'fecha_estimada_llegada' => $contenedor->fecha_estimada_llegada->format('Y-m-d'),
                'tipo' => $contenedor->tipo
            ];

        } catch (\Exception $e) {
            throw new SoapFault("SERVER", "Error al consultar el contenedor: " . $e->getMessage());
        }
    }
}