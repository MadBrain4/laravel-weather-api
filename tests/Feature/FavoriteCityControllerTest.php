<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\FavoriteCity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class FavoriteCityControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function it_returns_favorite_cities_for_authenticated_user()
    {
        $user = User::factory()->create();
        FavoriteCity::factory()->count(2)->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/weather/favorite-cities');

        $response->assertStatus(200)
                 ->assertJsonStructure(['data' => [['id', 'user_id', 'city']]]);
    }

    /** @test */
    public function it_allows_user_to_add_a_city_to_favorites()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/weather/favorite-cities', [
            'city' => 'London'
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'data' => ['id', 'user_id', 'city']
                 ]);
    }

    /** @test */
    public function it_requires_city_field_when_adding_favorite()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/weather/favorite-cities', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('city');
    }
}
