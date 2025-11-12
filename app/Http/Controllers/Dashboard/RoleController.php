<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Dashboard\RoleResource;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @OA\Tag(
 * name="Roles",
 * description="API Endpoints for managing roles"
 * )
 */
class RoleController extends Controller
{
    use ApiResponse, HasApiTokens, HasFactory, Notifiable;

    /**
     * @OA\Get(
     * path="/api/v1/roles",
     * summary="Get a list of roles",
     * description="Returns a paginated list of all roles.",
     * tags={"Roles"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="message", type="string", example="Roles retrieved successfully"),
     * @OA\Property(property="data", type="object",
     * @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/RoleResource")),
     * @OA\Property(property="pagination", type="object",
     * @OA\Property(property="total", type="integer", example=50),
     * @OA\Property(property="per_page", type="integer", example=15),
     * @OA\Property(property="current_page", type="integer", example=1),
     * @OA\Property(property="last_page", type="integer", example=4)
     * )
     * )
     * )
     * ),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index()
    {
        $roles = Role::paginate(config('pagination.perPage'));

        if (!$roles) {
            return $this->errorResponse('Role not found', 404);
        }

        return $this->successResponse('Role retrieved successfully', this->buildPaginatedResourceResponse(RoleResource::class, $roles), 200);
    }
}
