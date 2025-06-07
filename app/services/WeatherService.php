<?php
namespace App\Services;

use App\Models\User;
use App\Repositories\WeatherSearchRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;

class WeatherService
{
    public function __construct(protected WeatherSearchRepository $repository) {}

    public function getWeatherAndStore(User $user, string $city): array
    {
        $weather = $this->getCurrentWeather($city);
        $this->repository->store($user, $city, $weather);
        return $weather;
    }

    public function getUserSearchHistory(User $user, int $limit = 10): Collection
    {
        return $this->repository->getLatestUniqueCities($user, $limit);
    }

    public function getCurrentWeather(string $city): array
    {
        $lang = config('weather.lang', 'es');
        $cacheKey = "weather_{$city}_{$lang}";

        return Cache::remember($cacheKey, now()->addMinutes(config('weather.cache_minutes', 30)), function () use ($city, $lang) {
            $response = Http::get(config('weather.base_url') . '/current.json', [
                'key' => config('weather.key'),
                'q' => $city,
                'lang' => $lang,
            ]);

            if ($response->failed()) {
                throw new \Exception('No se pudo obtener el clima del proveedor externo.');
            }

            $data = $response->json();

            return [
                'ciudad' => $data['location']['name'],
                'hora_local' => $data['location']['localtime'],
                'temperatura_c' => $data['current']['temp_c'],
                'estado_clima' => $data['current']['condition']['text'],
                'humedad' => $data['current']['humidity'],
                'viento_kph' => $data['current']['wind_kph'],
            ];
        });
    }
}
