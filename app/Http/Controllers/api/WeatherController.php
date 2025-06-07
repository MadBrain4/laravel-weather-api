<?php 
    namespace App\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use App\Services\WeatherService;
    use Illuminate\Http\Request;
    use App\Http\Requests\api\WeatherRequest;
    use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

    class WeatherController extends Controller
    {
        public function __construct(protected WeatherService $weatherService) {}

        /**
         * Consultar el clima de una ciudad
         *
         * Este endpoint permite obtener información del clima actual de una ciudad específica. Al mismo tiempo, guarda esta consulta en el historial del usuario autenticado.
         *
         * @authenticated
         *
         * @bodyParam city string required Nombre de la ciudad a consultar. Example: Santiago
         *
         * @response 200 {
         *   "location": {
         *     "name": "Santiago",
         *     "region": "Region Metropolitana",
         *     "country": "Chile",
         *     "localtime": "2025-06-07 14:00"
         *   },
         *   "current": {
         *     "temp_c": 22.5,
         *     "condition": {
         *       "text": "Partly cloudy",
         *       "icon": "//cdn.weatherapi.com/weather/64x64/day/116.png"
         *     },
         *     "wind_kph": 15.2,
         *     "humidity": 45
         *   }
         * }
         *
         * @response 502 {
         *   "message": "No se pudo obtener la información del clima.",
         *   "error": "API key expired"
         * }
         */
        public function show(WeatherRequest $request): JsonResponse
        {
            Log::info($request->all());
            $city = $request->city;
            $user = $request->user();

            try {
                $weather = $this->weatherService->getWeatherAndStore($user, $city);
                return response()->json($weather);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => __('weather.fetch_error'),
                    'error' => $e->getMessage()
                ], 502);
            }
        }

        /**
         * Obtener historial de consultas del clima
         *
         * Devuelve un listado de ciudades previamente consultadas por el usuario autenticado, junto con la fecha y hora de cada consulta.
         *
         * @authenticated
         *
         * @response 200 {
         *   "history": [
         *     {
         *       "city": "Santiago",
         *       "consulted_at": "2025-06-07 14:00:00"
         *     },
         *     {
         *       "city": "Buenos Aires",
         *       "consulted_at": "2025-06-06 11:45:10"
         *     }
         *   ]
         * }
         */
        public function history(Request $request)
        {
            $user = $request->user();
            $history = $this->weatherService->getUserSearchHistory($user);

            return response()->json([
                'history' => $history->map(function ($item) {
                    return [
                        'city' => $item->city,
                        'consulted_at' => $item->created_at->toDateTimeString(),
                    ];
                }),
            ]);
        }
    }
