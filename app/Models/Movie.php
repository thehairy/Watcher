<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'tmdb_id',
        'title',
        'overview',
        'poster_path',
        'backdrop_path',
        'release_date',
        'runtime',
        'status',
        'vote_average',
        'vote_count',
        'genres',
        'budget',
        'revenue',
        'original_language',
        'original_title',
        'tagline',
    ];

    protected $casts = [
        'release_date' => 'date',
        'genres' => 'array',
        'vote_average' => 'decimal:1',
    ];

    public function watchlists(): HasMany
    {
        return $this->hasMany(Watchlist::class, 'watchable_id')
                    ->where('watchable_type', 'movie');
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

    public function getFormattedRuntimeAttribute(): string
    {
        if (!$this->runtime) {
            return '';
        }

        $hours = intval($this->runtime / 60);
        $minutes = $this->runtime % 60;

        return $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";
    }

    public function scopeByTmdbId($query, int $tmdbId)
    {
        return $query->where('tmdb_id', $tmdbId);
    }
}
