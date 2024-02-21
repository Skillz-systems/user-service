<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Service\LocationService;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    protected $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * @OA\Delete(
     *     path="/locations/{id}",
     *     summary="Deletes a specific location",
     *     operationId="deleteLocation",
     *     tags={"Locations"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The ID of the location to delete",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Location deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Location deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Location not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Location not found")
     *         )
     *     )
     * )
     */
    public function delete(int $id)
    {
        $result = $this->locationService->deleteLocation($id);

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Location deleted successfully'], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Location not found'], 404);
        }
    }

    /**
     * @OA\Post(
     *     path="/create_locations",
     *     summary="Create a new location",
     *     tags={"Locations"},
     *     @OA\RequestBody(
     *         description="Data for creating a new location",
     *         required=true,
     *         @OA\JsonContent(
     *             required={"location", "zone", "state"},
     *             @OA\Property(property="location", type="string", example="Downtown"),
     *             @OA\Property(property="zone", type="string", example="Commercial"),
     *             @OA\Property(property="state", type="string", example="Active"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Location created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/Location"
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(
     *                 property="error",
     *                 type="object",
     *                 @OA\Property(
     *                     property="location",
     *                     type="array",
     *                     @OA\Items(type="string", example="The location field is required.")
     *                 ),
     *                 @OA\Property(
     *                     property="zone",
     *                     type="array",
     *                     @OA\Items(type="string", example="The zone field is required.")
     *                 ),
     *                 @OA\Property(
     *                     property="state",
     *                     type="array",
     *                     @OA\Items(type="string", example="The state field is required.")
     *                 ),
     *             ),
     *         ),
     *     ),
     * )
     */
    public function create(Request $request)
    {
        $result = $this->locationService->create($request);

        if ($result instanceof Location) {
            return response()->json(['success' => true, 'data' => $result], 201);
        } else {
            return response()->json(['success' => false, 'error' => $result], 422);
        }
    }
}
