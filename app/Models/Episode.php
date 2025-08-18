<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Episode extends Model
{
    use HasFactory;

    protected $fillable = [
        'show_id',
        'tmdb_id',
        'name',
        'overview',
        'still_path',
        'season_number',
        'episode_number',
        'air_date',
        'runtime',
        'vote_average',
        'vote_count',
    ];

    protected $casts = [
        'air_date' => 'date',
        'vote_average' => 'decimal:1',
    ];

    public function show(): BelongsTo
    {
        return $this->belongsTo(Show::class);
    }

    public function watchProgress(): HasMany
    {
        return $this->hasMany(WatchProgress::class);
    }

    public function getFullStillUrlAttribute(): ?string
    {
        if (!$this->still_path) {
            return null;
        }

        return app(\App\Services\TmdbService::class)->getImageUrl($this->still_path, 'w300');
    }

    public function getFormattedEpisodeAttribute(): string
    {
        return "S{$this->season_number}E{$this->episode_number}";
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
}
