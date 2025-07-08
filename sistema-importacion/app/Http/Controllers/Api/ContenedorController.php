<?php
namespace App\Http\Controllers\Api;

use App\Models\Contenedor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


/**
 * @OA\Tag(
 *     name="Contenedores",
 *     description="Operaciones CRUD para contenedores"
 * )
 */
class ContenedorController extends BaseController
{
    public function index(): JsonResponse
    {
        $contenedores = Contenedor::with('productos')->get();
        return $this->sendResponse($contenedores, 'Contenedores obtenidos exitosamente.');
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'numero_contenedor' => 'required|string|unique:contenedores,numero_contenedor',
            'tipo' => 'required|in:20ft,40ft,reefer,45ft',
            'fecha_estimada_llegada' => 'required|date',
            'estado' => 'nullable|in:en_transito,llegado,descargado',
            'productos' => 'nullable|array',
            'productos.*.producto_id' => 'required_with:productos|exists:productos_importados,id',
            'productos.*.cantidad' => 'required_with:productos|integer|min:1'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error de validación.', $validator->errors(), 422);
        }

        DB::beginTransaction();
        try {
            $contenedor = Contenedor::create([
                'numero_contenedor' => $request->numero_contenedor,
                'tipo' => $request->tipo,
                'fecha_estimada_llegada' => $request->fecha_estimada_llegada,
                'estado' => $request->estado ?? 'en_transito'
            ]);

            if ($request->has('productos')) {
                foreach ($request->productos as $productoData) {
                    $contenedor->productos()->attach($productoData['producto_id'], [
                        'cantidad' => $productoData['cantidad']
                    ]);
                }
            }

            $contenedor->load('productos');

            DB::commit();
            return $this->sendResponse($contenedor, 'Contenedor creado exitosamente.', 201);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Error al crear el contenedor.', [$e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $contenedor = Contenedor::find($id);

        if (!$contenedor) {
            return $this->sendError('Contenedor no encontrado.', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'numero_contenedor' => 'sometimes|required|string|unique:contenedores,numero_contenedor,' . $id,
            'tipo' => 'sometimes|required|in:20ft,40ft,reefer,45ft',
            'fecha_estimada_llegada' => 'sometimes|required|date',
            'estado' => 'sometimes|nullable|in:en_transito,llegado,descargado',
            'productos' => 'sometimes|nullable|array',
            'productos.*.producto_id' => 'required_with:productos|exists:productos_importados,id',
            'productos.*.cantidad' => 'required_with:productos|integer|min:1'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error de validación.', $validator->errors(), 422);
        }

        DB::beginTransaction();
        try {
            // Actualizar los campos del contenedor
            $contenedor->update($request->only([
                'numero_contenedor',
                'tipo', 
                'fecha_estimada_llegada',
                'estado'
            ]));

            // Si se proporcionan productos, actualizar la relación
            if ($request->has('productos')) {
                // Primero eliminar todas las relaciones existentes
                $contenedor->productos()->detach();
                
                // Luego agregar las nuevas relaciones
                if (!empty($request->productos)) {
                    foreach ($request->productos as $productoData) {
                        $contenedor->productos()->attach($productoData['producto_id'], [
                            'cantidad' => $productoData['cantidad']
                        ]);
                    }
                }
            }

            $contenedor->load('productos');

            DB::commit();
            return $this->sendResponse($contenedor, 'Contenedor actualizado exitosamente.');
            
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Error al actualizar el contenedor.', [$e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        $contenedor = Contenedor::find($id);

        if (!$contenedor) {
            return $this->sendError('Contenedor no encontrado.');
        }

        $contenedor->delete();
        return $this->sendResponse([], 'Contenedor eliminado exitosamente.');
    }

    public function consultarEstado($numeroContenedor): JsonResponse
    {
        $contenedor = Contenedor::where('numero_contenedor', $numeroContenedor)->first();

        if (!$contenedor) {
            return $this->sendError('Contenedor no encontrado.');
        }

        $response = [
            'numero_contenedor' => $contenedor->numero_contenedor,
            'estado' => $contenedor->estado,
            'fecha_estimada_llegada' => $contenedor->fecha_estimada_llegada->format('Y-m-d'),
            'tipo' => $contenedor->tipo
        ];

        return $this->sendResponse($response, 'Estado del contenedor obtenido exitosamente.');
    }
}