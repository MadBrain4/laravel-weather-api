<?php
// routes/api.php
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\Api\WeatherController;
use App\Http\Controllers\api\FavoriteCityController;
use Illuminate\Support\Facades\Route;

// Rutas de autenticación
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->get('/me', [AuthController::class, 'me']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
});

// Rutas de idiomas
Route::middleware('auth:sanctum')->prefix('user')->group(function () {
    // Rutas protegidas por el middleware de superadmin
    Route::middleware(['superadmin'])->patch('/{user}/role', [UserController::class, 'changeUserRole']);

    Route::patch('/language', [UserController::class, 'updateLanguage']);
    Route::get('/language', [UserController::class, 'getLanguage']);
});

// Rutas de clima
Route::middleware('auth:sanctum')->prefix('weather')->group(function () {
    Route::get('/', [WeatherController::class, 'show']);
    Route::get('/favorite-cities', [FavoriteCityController::class, 'index']);
    Route::post('/favorite-cities', [FavoriteCityController::class, 'store']);
});
