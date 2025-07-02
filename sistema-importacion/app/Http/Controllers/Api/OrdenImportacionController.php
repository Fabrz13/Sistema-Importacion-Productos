<?php
namespace App\Http\Controllers\Api;

use App\Models\OrdenImportacion;
use App\Models\ProductoImportado;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Órdenes de Importación",
 *     description="Operaciones CRUD para órdenes de importación"
 * )
 */
class OrdenImportacionController extends BaseController
{
    public function index(): JsonResponse
    {
        $ordenes = OrdenImportacion::with(['proveedor', 'productos'])->get();
        return $this->sendResponse($ordenes, 'Órdenes obtenidas exitosamente.');
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'fecha_emision' => 'required|date',
            'proveedor_id' => 'required|exists:proveedores,id',
            'estado' => 'nullable|in:pendiente,embarcado,recibido',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos_importados,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio_unitario' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error de validación.', $validator->errors(), 422);
        }

        DB::beginTransaction();
        try {
            $orden = OrdenImportacion::create([
                'fecha_emision' => $request->fecha_emision,
                'proveedor_id' => $request->proveedor_id,
                'estado' => $request->estado ?? 'pendiente'
            ]);

            $valorTotal = 0;
            foreach ($request->productos as $productoData) {
                $subtotal = $productoData['cantidad'] * $productoData['precio_unitario'];
                $valorTotal += $subtotal;

                $orden->productos()->attach($productoData['producto_id'], [
                    'cantidad' => $productoData['cantidad'],
                    'precio_unitario' => $productoData['precio_unitario']
                ]);
            }

            $orden->update(['valor_total' => $valorTotal]);
            $orden->load(['proveedor', 'productos']);

            DB::commit();
            return $this->sendResponse($orden, 'Orden creada exitosamente.', 201);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Error al crear la orden.', [$e->getMessage()], 500);
        }
    }

    public function show($id): JsonResponse
    {
        $orden = OrdenImportacion::with(['proveedor', 'productos'])->find($id);

        if (!$orden) {
            return $this->sendError('Orden no encontrada.');
        }

        return $this->sendResponse($orden, 'Orden obtenida exitosamente.');
    }

    public function update(Request $request, $id): JsonResponse
    {
        $orden = OrdenImportacion::find($id);

        if (!$orden) {
            return $this->sendError('Orden no encontrada.');
        }

        $validator = Validator::make($request->all(), [
            'fecha_emision' => 'required|date',
            'proveedor_id' => 'required|exists:proveedores,id',
            'estado' => 'nullable|in:pendiente,embarcado,recibido'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error de validación.', $validator->errors(), 422);
        }

        $orden->update($request->all());
        $orden->load(['proveedor', 'productos']);
        
        return $this->sendResponse($orden, 'Orden actualizada exitosamente.');
    }

    public function destroy($id): JsonResponse
    {
        $orden = OrdenImportacion::find($id);

        if (!$orden) {
            return $this->sendError('Orden no encontrada.');
        }

        $orden->delete();
        return $this->sendResponse([], 'Orden eliminada exitosamente.');
    }

    public function updateEstado(Request $request, $id): JsonResponse
    {
        $orden = OrdenImportacion::find($id);

        if (!$orden) {
            return $this->sendError('Orden no encontrada.');
        }

        $validator = Validator::make($request->all(), [
            'estado' => 'required|in:pendiente,embarcado,recibido'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error de validación.', $validator->errors(), 422);
        }

        $orden->update(['estado' => $request->estado]);
        return $this->sendResponse($orden, 'Estado actualizado exitosamente.');
    }
}