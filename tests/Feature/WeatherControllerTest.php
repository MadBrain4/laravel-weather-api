<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WeatherControllerTest extends TestCase
{
    public function it_returns_weather_data_for_valid_city()
    {
        Sanctum::actingAs($user = User::factory()->create());

        // Simulamos la respuesta de WeatherAPI
        Http::fake([
            '*' => Http::response([
                'location' => [
                    'name' => 'London',
                    'localtime' => '2024-01-01 12:00',
                ],
                'current' => [
                    'temp_c' => 20,
                    'condition' => ['text' => 'Soleado'],
                    'humidity' => 50,
                    'wind_kph' => 10,
                ],
            ], 200)
        ]);

        $response = $this->getJson('/api/weather?city=London');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'ciudad', 'hora_local', 'temperatura_c', 'estado_clima', 'humedad', 'viento_kph'
                 ]);
    }

    /** @test */
    public function it_returns_error_for_invalid_city()
    {
        Sanctum::actingAs($user = User::factory()->create());

        Http::fake([
            '*' => Http::response([], 400)
        ]);

        $response = $this->getJson('/api/weather?city=InvalidCity');

        $response->assertStatus(502)
                 ->assertJsonStructure([
                     'message',
                     'error'
                 ]);
    }

    /** @test */
    public function it_returns_search_history_for_user()
    {
        Sanctum::actingAs($user = User::factory()->create());

        // Creamos búsquedas manualmente
        $user->weatherSearches()->createMany([
            ['city' => 'London', 'data' => []],
            ['city' => 'Berlin', 'data' => []],
        ]);

        $response = $this->getJson('/api/weather/history');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'history' => [
                         ['city', 'consulted_at'],
                     ]
                 ])
                 ->assertJsonCount(2, 'history');
    }
}
