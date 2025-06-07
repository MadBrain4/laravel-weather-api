<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FavoriteCityService;

class FavoriteCityController extends Controller
{
    protected FavoriteCityService $favoriteService;

    public function __construct(FavoriteCityService $favoriteService)
    {
        $this->favoriteService = $favoriteService;
    }

    public function index(Request $request)
    {
        $favorites = $request->user()->favoriteCities()->get();

        return response()->json(['data' => $favorites]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'city' => 'required|string',
        ]);

        $city = $request->input('city');
        $user = $request->user();

        $favorite = $this->favoriteService->addCityToFavorites($user, $city);

        return response()->json([
            'message' => __('weather.city_added_to_favorites'),
            'data' => $favorite,
        ]);
    }
}
