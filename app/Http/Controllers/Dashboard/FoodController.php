<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Resources\Dashboard\FoodResource;
use App\Models\Food;
use App\Models\User;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 * name="Foods",
 * description="API Endpoints for managing foods"
 * )
 */
class FoodController extends Controller
{
    use ApiResponse;

    /**
     * @OA\Get(
     * path="/api/v1/foods",
     * summary="Get a list of foods",
     * description="Returns a paginated list of all foods.",
     * tags={"Foods"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * type="object",
     * @OA\Property(property="message", type="string", example="Food retrieved successfully"),
     * @OA\Property(property="data", type="object",
     * @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/FoodResource")),
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
        $food = Food::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(config('pagination.perPage'));

        return $this->successResponse('User retrieved successfully', $this->buildPaginatedResourceResponse(FoodResource::class, $food), 200);
    }

    /**
     * @OA\Post(
     * path="/api/v1/foods",
     * summary="Create a new user's food",
     * description="Creates a new user's food.",
     * tags={"Foods"},
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"email", "title", "description", "ingredients", "nutrition", "image"},
     * @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     * @OA\Property(property="title", type="string", example="Doloremque blanditi"),
     * @OA\Property(property="description", type="text", example="Lorem ipsum dolor sit amet consectetur adipisicing elit. Laudantium reprehenderit incidunt nemo ipsam qui. Architecto, autem officiis ipsum dolores illo neque dolorum accusantium fugit ab, nemo cum magni illum veniam!"),
     * @OA\Property(property="nutrition", type="string", example="Voluptas explicabo rerum molestiae totam repudiandae ut. Sapiente eos doloremque veritatis voluptatem. Amet voluptatem sed itaque sed non."),
     * @OA\Property(property="image", type="string", format="file", example="foodexample.jpg"),
     *
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Food created successfully",
     * @OA\JsonContent(ref="#/components/schemas/FoodResource")
     * ),
     * @OA\Response(response=422, description="Validation error"),
     * @OA\Response(response=500, description="Food creation failed"),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'title' => 'required',
            'description' => 'required|string|max:255',
            'ingredients' => 'required',
            'nutrition' => 'required',
            'image' => 'required|mimes:jpg|jpeg|png|heic',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $userId = User::where('email', $request->email)->first();
        $createdByUser = Auth::user()->id;

        if ($request->hasFile('image')) {
            $uploadedFile = Cloudinary::upload($request->file('image')->getRealPath(), ['folder' => 'foods'])->getSecurePath();
            $imageUrl = $uploadedFile['secure_url'];
            $imagePublicId = $uploadedFile['public_id'];
        }

        try {
            $food = Food::create([
                'user_id' => $userId,
                'title' => $request->title,
                'description' => $request->description,
                'created_by' => $createdByUser,
                'ingredients' => $request->ingredients,
                'nutrition' => $request->nutrition,
                'image_url' => $imageUrl,
                'image_public_id' => $imagePublicId
            ]);

            return $this->successResponse('Food created successfully', new FoodResource($food), 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Food creation failed:' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/v1/foods/{id}",
     * summary="Get a single food",
     * description="Returns the details of a specific food by their ID.",
     * tags={"Foods"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID of the food",
     * @OA\Schema(type="string")
     * ),
     * @OA\Response(
     * response=200,
     * description="Food Fetched Successfully",
     * @OA\JsonContent(ref="#/components/schemas/FoodResource")
     * ),
     * @OA\Response(response=404, description="Food not found"),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function show($id)
    {
        $food = Food::find($id);

        if (!$food) {
            return $this->errorResponse('Food not found.', 404);
        }

        return $this->successResponse('Food Fetched Successfully', new FoodResource($food), 200);
    }

    /**
     * @OA\Put(
     * path="/api/v1/foods/{id}",
     * summary="Update an existing food",
     * description="Updates the details of an existing food.",
     * tags={"Foods"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID of the food to update",
     * @OA\Schema(type="string")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"email", "title", "description", "ingredients", "nutrition", "image"},
     * @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     * @OA\Property(property="title", type="string", example="Doloremque blanditi"),
     * @OA\Property(property="description", type="text", example="Lorem ipsum dolor sit amet consectetur adipisicing elit. Laudantium reprehenderit incidunt nemo ipsam qui. Architecto, autem officiis ipsum dolores illo neque dolorum accusantium fugit ab, nemo cum magni illum veniam!"),
     * @OA\Property(property="nutrition", type="string", example="Voluptas explicabo rerum molestiae totam repudiandae ut. Sapiente eos doloremque veritatis voluptatem. Amet voluptatem sed itaque sed non."),
     * @OA\Property(property="image", type="string", format="file", example="foodexample.jpg"),
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Food Updated Successfully",
     * @OA\JsonContent(ref="#/components/schemas/FoodResource")
     * ),
     * @OA\Response(response=422, description="Validation error"),
     * @OA\Response(response=404, description="Food not found"),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function update(Request $request, $id)
    {
        $food = Food::find($id);

        if (!$food) {
            return $this->errorResponse('Food not found.', 404);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'title' => 'required',
            'description' => 'required|string|max:255',
            'ingredients' => 'required',
            'nutrition' => 'required',
            'image' => 'nullable',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        $foodData = [
            'email' => $request->email,
            'title' => $request->title,
            'description' => $request->description,
            'ingredients' => $request->ingredients,
            'nutrition' => $request->nutrition
        ];

        if ($request->hasFile($request->image)) {
            if ($food->profile_public_id) {
                Cloudinary::destroy($food->profile_public_id);
            }

            $uploadedFile = Cloudinary::upload($request->file('image')->getRealPath(), ['folder' => 'foods'])->getSecurePath();
            $foodData['image_url'] = $uploadedFile['secure_url'];
            $foodData['image_public_id'] = $uploadedFile['public_id'];
        }

        try {
            $food->update($foodData);

            return $this->successResponse('Food updated Successfully', new FoodResource($food), 200);
        } catch (\Exception $e) {
            return $this->errorResponse('Food updated failed:' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/foods/{id}",
     *     summary="Delete a specific food",
     *     description="This endpoint permanently deletes a food record from the system using its unique ID.",
     *     tags={"Foods"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the food to delete",
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *
     *     @OA\Response(
     *         response=204,
     *         description="Food deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Food deleted successfully.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Food not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Food not found.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Something went wrong while deleting food.")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $food = Food::find($id);

        if (!$food) {
            return $this->errorResponse('Food not found.', 404);
        }

        if ($food->image_public_id) {
            Cloudinary::destroy($food->image_public_id);
        }

        $food->delete();

        return $this->successResponse('Food deleted Successfully', null, 204);
    }
}
