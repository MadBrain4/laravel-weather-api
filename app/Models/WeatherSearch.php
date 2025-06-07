<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class WeatherSearch extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'city',
        'data',
    ];

    protected $casts = [
        'data' => 'array', 
    ];

    /**
     * Relación con el usuario que hizo la búsqueda.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
