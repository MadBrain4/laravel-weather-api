<?php 
    namespace App\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use App\Services\WeatherService;
    use Illuminate\Http\Request;
    use App\Http\Requests\api\WeatherRequest;
    use Illuminate\Http\JsonResponse;

    class WeatherController extends Controller
    {
        public function __construct(protected WeatherService $weatherService) {}

        public function show(WeatherRequest $request): JsonResponse
        {
            $city = $request->validated()['city'];
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
