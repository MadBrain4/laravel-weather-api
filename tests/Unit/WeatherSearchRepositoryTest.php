<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\WeatherSearch;
use App\Repositories\WeatherSearchRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeatherSearchRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected WeatherSearchRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new WeatherSearchRepository();
    }

    /** @test */
    public function it_stores_a_weather_search_for_a_user()
    {
        $user = User::factory()->create();
        $city = 'Santiago';
        $data = [
            'ciudad' => 'Santiago',
            'temperatura_c' => 25,
            'estado_clima' => 'Soleado',
            'hora_local' => '2025-06-06 12:00',
            'humedad' => 60,
            'viento_kph' => 12,
        ];

        $search = $this->repository->store($user, $city, $data);

        $this->assertInstanceOf(WeatherSearch::class, $search);
        $this->assertEquals($city, $search->city);
        $this->assertDatabaseHas('weather_searches', [
            'user_id' => $user->id,
            'city' => $city,
        ]);
    }

    /** @test */
    public function it_returns_latest_unique_cities()
    {
        $user = User::factory()->create();

        $cities = ['Santiago', 'Lima', 'Bogotá', 'Lima', 'Quito', 'Santiago'];

        foreach ($cities as $city) {
            WeatherSearch::factory()->create([
                'user_id' => $user->id,
                'city' => $city,
                'data' => ['fake' => 'data'],
                'created_at' => now()->addMinutes(rand(1, 100)),
            ]);
        }

        $results = $this->repository->getLatestUniqueCities($user);

        $this->assertCount(4, $results); // Santiago, Lima, Bogotá, Quito (únicos)
        $this->assertEqualsCanonicalizing(
            ['Santiago', 'Lima', 'Bogotá', 'Quito'],
            $results->pluck('city')->toArray()
        );
    }
}
