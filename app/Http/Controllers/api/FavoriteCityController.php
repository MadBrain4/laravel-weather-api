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

        /**
     * Obtener ciudades favoritas
     *
     * Devuelve una lista de ciudades marcadas como favoritas por el usuario autenticado.
     *
     * @authenticated
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "user_id": 5,
     *       "city": "Santiago",
     *       "created_at": "2025-06-07T14:00:00.000000Z",
     *       "updated_at": "2025-06-07T14:00:00.000000Z"
     *     }
     *   ]
     * }
     */
    public function index(Request $request)
    {
        $favorites = $request->user()->favoriteCities()->get();

        return response()->json(['data' => $favorites]);
    }

        /**
     * Agregar ciudad a favoritos
     *
     * Permite al usuario autenticado agregar una ciudad a su lista de favoritas.
     *
     * @authenticated
     *
     * @bodyParam city string required Nombre de la ciudad que se desea agregar. Example: Santiago
     *
     * @response 200 {
     *   "message": "Ciudad agregada a favoritos correctamente",
     *   "data": {
     *     "id": 2,
     *     "user_id": 5,
     *     "city": "Santiago",
     *     "created_at": "2025-06-07T14:01:00.000000Z",
     *     "updated_at": "2025-06-07T14:01:00.000000Z"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "city": [
     *       "The city field is required."
     *     ]
     *   }
     * }
     */
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
