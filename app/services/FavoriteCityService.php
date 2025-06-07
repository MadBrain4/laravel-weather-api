<?php 
    namespace App\Services;

    use App\Models\User;
    use App\Repositories\FavoriteCityRepository;
    use Illuminate\Support\Facades\Cache;
    use Illuminate\Support\Facades\Http;

    class FavoriteCityService
    {
        protected FavoriteCityRepository $favoriteRepo;

        public function __construct(FavoriteCityRepository $favoriteRepo)
        {
            $this->favoriteRepo = $favoriteRepo;
        }

        public function addCityToFavorites(User $user, string $city)
        {
            if (!$this->validateCityExists($city)) {
                throw new \Exception(__('weather.city_not_found'));
            }

            return $this->favoriteRepo->store($user, $city);
        }

        protected function validateCityExists(string $city): bool
        {
            // Revisar primero en caché
            $cacheKey = "weather_{$city}";

            if (Cache::has($cacheKey)) {
                return true;
            }

            // Si no está en caché, validar con la API (y opcionalmente guardar la respuesta)
            $response = Http::get(config('weather.base_url') . '/current.json', [
                'key' => config('weather.key'),
                'q' => $city,
            ]);

            if ($response->successful()) {
                Cache::put($cacheKey, $response->json(), now()->addMinutes(config('weather.cache_minutes', 30)));
                return true;
            }

            return false;
        }
    }
