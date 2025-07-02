<?php
namespace App\Http\Controllers\Api;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Proveedores",
 *     description="Operaciones CRUD para proveedores"
 * )
 */
class ProveedorController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/proveedores",
     *     tags={"Proveedores"},
     *     summary="Listar todos los proveedores",
     *     @OA\Response(response=200, description="Lista de proveedores")
     * )
     */
    public function index(): JsonResponse
    {
        $proveedores = Proveedor::with('ordenesImportacion')->get();
        return $this->sendResponse($proveedores, 'Proveedores obtenidos exitosamente.');
    }

    /**
     * @OA\Post(
     *     path="/api/proveedores",
     *     tags={"Proveedores"},
     *     summary="Crear un nuevo proveedor",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombre","pais","email"},
     *             @OA\Property(property="nombre", type="string"),
     *             @OA\Property(property="pais", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="telefono", type="string"),
     *             @OA\Property(property="direccion", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Proveedor creado exitosamente")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'pais' => 'required|string|max:255',
            'email' => 'required|email|unique:proveedores,email',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error de validación.', $validator->errors(), 422);
        }

        $proveedor = Proveedor::create($request->all());
        return $this->sendResponse($proveedor, 'Proveedor creado exitosamente.', 201);
    }

    /**
     * @OA\Get(
     *     path="/api/proveedores/{id}",
     *     tags={"Proveedores"},
     *     summary="Obtener un proveedor específico",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Proveedor obtenido exitosamente")
     * )
     */
    public function show($id): JsonResponse
    {
        $proveedor = Proveedor::with('ordenesImportacion')->find($id);

        if (!$proveedor) {
            return $this->sendError('Proveedor no encontrado.');
        }

        return $this->sendResponse($proveedor, 'Proveedor obtenido exitosamente.');
    }

    /**
     * @OA\Put(
     *     path="/api/proveedores/{id}",
     *     tags={"Proveedores"},
     *     summary="Actualizar un proveedor",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Proveedor actualizado exitosamente")
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        $proveedor = Proveedor::find($id);

        if (!$proveedor) {
            return $this->sendError('Proveedor no encontrado.');
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'pais' => 'required|string|max:255',
            'email' => 'required|email|unique:proveedores,email,' . $id,
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error de validación.', $validator->errors(), 422);
        }

        $proveedor->update($request->all());
        return $this->sendResponse($proveedor, 'Proveedor actualizado exitosamente.');
    }

    /**
     * @OA\Delete(
     *     path="/api/proveedores/{id}",
     *     tags={"Proveedores"},
     *     summary="Eliminar un proveedor",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Proveedor eliminado exitosamente")
     * )
     */
    public function destroy($id): JsonResponse
    {
        $proveedor = Proveedor::find($id);

        if (!$proveedor) {
            return $this->sendError('Proveedor no encontrado.');
        }

        $proveedor->delete();
        return $this->sendResponse([], 'Proveedor eliminado exitosamente.');
    }
}