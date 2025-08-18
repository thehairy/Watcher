<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Show extends Model
{
    use HasFactory;

    protected $fillable = [
        'tmdb_id',
        'name',
        'overview',
        'poster_path',
        'backdrop_path',
        'first_air_date',
        'last_air_date',
        'number_of_seasons',
        'number_of_episodes',
        'status',
        'vote_average',
        'vote_count',
        'genres',
        'networks',
        'in_production',
        'original_language',
        'original_name',
    ];

    protected $casts = [
        'first_air_date' => 'date',
        'last_air_date' => 'date',
        'genres' => 'array',
        'networks' => 'array',
        'in_production' => 'boolean',
        'vote_average' => 'decimal:1',
    ];

    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class);
    }

    public function watchlists(): HasMany
    {
        return $this->hasMany(Watchlist::class, 'watchable_id')
                    ->where('watchable_type', 'show');
    }

    public function watchProgress(): HasMany
    {
        return $this->hasMany(WatchProgress::class);
    }

    public function getFullPosterUrlAttribute(): ?string
    {
        if (!$this->poster_path) {
            return null;
        }

        return app(\App\Services\TmdbService::class)->getImageUrl($this->poster_path, 'w500');
    }

    public function getFullBackdropUrlAttribute(): ?string
    {
        if (!$this->backdrop_path) {
            return null;
        }

        return app(\App\Services\TmdbService::class)->getImageUrl($this->backdrop_path, 'w1280');
    }

    public function getFormattedGenresAttribute(): string
    {
        if (!$this->genres) {
            return '';
        }

        return collect($this->genres)->pluck('name')->implode(', ');
    }

    public function scopeByTmdbId($query, int $tmdbId)
    {
        return $query->where('tmdb_id', $tmdbId);
    }
}
