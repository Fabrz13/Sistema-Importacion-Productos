<?php
namespace App\Http\Controllers\Api;

use App\Models\ProductoImportado;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Productos Importados",
 *     description="Operaciones CRUD para productos importados"
 * )
 */
class ProductoImportadoController extends BaseController
{
    public function index(): JsonResponse
    {
        $productos = ProductoImportado::with(['ordenesImportacion', 'contenedores'])->get();
        return $this->sendResponse($productos, 'Productos obtenidos exitosamente.');
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'codigo_arancelario' => 'required|string|max:50',
            'precio_fob' => 'required|numeric|min:0',
            'unidad_medida' => 'nullable|string|max:50'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error de validación.', $validator->errors(), 422);
        }

        $producto = ProductoImportado::create($request->all());
        return $this->sendResponse($producto, 'Producto creado exitosamente.', 201);
    }

    public function show($id): JsonResponse
    {
        $producto = ProductoImportado::with(['ordenesImportacion', 'contenedores'])->find($id);

        if (!$producto) {
            return $this->sendError('Producto no encontrado.');
        }

        return $this->sendResponse($producto, 'Producto obtenido exitosamente.');
    }

    public function update(Request $request, $id): JsonResponse
    {
        $producto = ProductoImportado::find($id);

        if (!$producto) {
            return $this->sendError('Producto no encontrado.');
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'codigo_arancelario' => 'required|string|max:50',
            'precio_fob' => 'required|numeric|min:0',
            'unidad_medida' => 'nullable|string|max:50'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error de validación.', $validator->errors(), 422);
        }

        $producto->update($request->all());
        return $this->sendResponse($producto, 'Producto actualizado exitosamente.');
    }

    public function destroy($id): JsonResponse
    {
        $producto = ProductoImportado::find($id);

        if (!$producto) {
            return $this->sendError('Producto no encontrado.');
        }

        $producto->delete();
        return $this->sendResponse([], 'Producto eliminado exitosamente.');
    }
}