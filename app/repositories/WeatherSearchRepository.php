<?php
    namespace App\Repositories;

    use App\Models\User;
    use App\Models\WeatherSearch;
    use Illuminate\Support\Collection;

    class WeatherSearchRepository
    {
        public function store(User $user, string $city, array $data): WeatherSearch
        {
            return $user->weatherSearches()->create([
                'city' => $city,
                'data' => $data,
            ]);
        }

        public function getLatestUniqueCities(User $user, int $limit = 10): Collection
        {
            return $user->weatherSearches()
                ->orderByDesc('created_at')
                ->get()
                ->unique('city')
                ->take($limit)
                ->values();
        }
    }

