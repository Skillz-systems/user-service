<?php

namespace App\Http\Controllers;

use App\Http\Resources\UnitResource;
use App\Models\Unit;
use App\Service\UnitService;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    protected $unitService;

    public function __construct(UnitService $unitService)
    {
        $this->unitService = $unitService;
    }

    /**
     * @OA\Post(
     *     path="/unit",
     *     summary="Create a new unit",
     *     tags={"Unit"},
     *     operationId="createUnit",
     *     @OA\RequestBody(
     *         description="Data for creating a new unit",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UnitRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Unit created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Unit created successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="error", type="string", example="Error details")
     *         )
     *     ),
     *     security={
     *         {"api_key": {}}
     *     }
     * )
     */
    public function create(Request $request)
    {
        $result = $this->unitService->create($request);

        if ($result instanceof Unit) {
            return response()->json(['success' => true, 'message' => 'Unit created successfully'], 201);
        } else {
            return response()->json(['success' => false, 'error' => $result], 422);
        }
    }

    /**
     * @OA\Get(
     *     path="/units",
     *     operationId="viewAllUnits",
     *     tags={"Units"},
     *     summary="Get list of all units",
     *     description="Returns a list of all units.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Unit 1"),
     *                     // Define other properties of a unit here
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     ),
     * )
     */
    public function index()
    {

        $units = $this->unitService->viewAllUnits();

        return response()->json(['success' => true, 'data' => new UnitResource($units)], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/unit/{id}",
     *     summary="Delete a unit by ID",
     *     tags={"Units"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the unit to delete",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success response",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Unit successfully deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unit not found")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {

        $result = $this->unitService->deleteUnit($id);

        $response = $result
            ? ['success' => true, 'message' => 'Unit successfully deleted']
            : ['success' => false, 'message' => 'Unit not found'];

        return response()->json($response, $result ? 200 : 404);
    }

    /**
     * @OA\Get(
     *     path="/units/{id}",
     *     operationId="getUnitById",
     *     tags={"Units"},
     *     summary="Get unit information",
     *     description="Returns unit data",
     *     @OA\Parameter(
     *         name="id",
     *         description="Unit id",
     *         required=true,
     *         in="path",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/Unit")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Unit not found"
     *     ),
     *     security={
     *         {"api_key": {}}
     *     }
     * )
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {

        $unit = $this->unitService->getUnit($id);

        if (!$unit) {
            return response()->json(['success' => false, 'error' => 'Unit not found'], 404);
        }

        return response()->json(['success' => true, 'data' => new UnitResource($unit)], 200);
    }
    /**
     * Retrieve units in a department.
     *
     * @OA\Get(
     *      path="/departments/{departmentId}/units",
     *      operationId="getUnitsInDepartment",
     *      tags={"Units"},
     *      summary="Get units in a department",
     *      description="Retrieves units belonging to a specific department.",
     *      @OA\Parameter(
     *          name="departmentId",
     *          in="path",
     *          required=true,
     *          description="ID of the department",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean",
     *                  example=true,
     *                  description="Indicates whether the request was successful."
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/Unit"),
     *                  description="Array of units belonging to the department"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  example="Units successfully retrieved.",
     *                  description="Success message"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean",
     *                  example=false,
     *                  description="Indicates whether the request was successful."
     *              ),
     *              @OA\Property(
     *                  property="error",
     *                  type="mixed",
     *                  description="Error message or data"
     *              )
     *          )
     *      )
     * )
     *
     * @param int $departmentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUnitsInDepartment($departmentId)
    {
        $result = $this->unitService->getUnitsInDepartment($departmentId);

        if ($result)
            return response()->json(['success' => true, 'data' => $result, 'message' => 'Units successfully retrieved.'], 200);

        return response()->json(['success' => false, 'error' => $result], 422);
    }
}
