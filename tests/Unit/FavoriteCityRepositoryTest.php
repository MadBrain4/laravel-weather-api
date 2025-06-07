<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\FavoriteCity;
use App\Repositories\FavoriteCityRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteCityRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected FavoriteCityRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new FavoriteCityRepository();
    }

    /** @test */
    public function it_stores_a_favorite_city_for_a_user()
    {
        $user = User::factory()->create();

        $city = 'Santiago';

        $favorite = $this->repository->store($user, $city);

        $this->assertInstanceOf(FavoriteCity::class, $favorite);
        $this->assertEquals($city, $favorite->city);
        $this->assertDatabaseHas('favorite_cities', [
            'user_id' => $user->id,
            'city' => $city,
        ]);
    }

    /** @test */
    public function it_does_not_duplicate_favorite_city()
    {
        $user = User::factory()->create();
        $city = 'Santiago';

        $first = $this->repository->store($user, $city);
        $second = $this->repository->store($user, $city);

        $this->assertEquals($first->id, $second->id);
        $this->assertCount(1, $user->favoriteCities);
    }
}
