<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Client\FoodResource;
use App\Models\Food;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    public function index($userId)
    {
        $food = Food::where('user_id', $userId)->paginate(config(('pagination.perPage')));

        return $this->successResponse('User retrieved successfully', $this->buildPaginatedResourceResponse(FoodResource::class, $food), 200);
    }

    public function show($userId, $id)
    {
        $food = Food::where('user_id', $userId)->where('id', $id)->first();

        return $this->successResponse('Food retrieved successfully', new FoodResource($food), 200);
    }
}
