<?php
    namespace App\Repositories;

    use App\Models\User;
    use App\Models\FavoriteCity;

    class FavoriteCityRepository
    {
        public function store(User $user, string $city): FavoriteCity
        {
            return $user->favoriteCities()->firstOrCreate(['city' => $city]);
        }
    }