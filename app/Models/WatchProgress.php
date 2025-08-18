<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WatchProgress extends Model
{
    use HasFactory;

    protected $table = 'watch_progress';

    protected $fillable = [
        'user_id',
        'episode_id',
        'show_id',
        'tmdb_episode_id',
        'tmdb_show_id',
        'watched',
        'watched_at',
        'watch_time',
        'progress',
    ];

    protected $casts = [
        'watched' => 'boolean',
        'watched_at' => 'datetime',
        'progress' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function episode(): BelongsTo
    {
        return $this->belongsTo(Episode::class);
    }

    public function show(): BelongsTo
    {
        return $this->belongsTo(Show::class);
    }

    public function markAsWatched(): void
    {
        $this->update([
            'watched' => true,
            'watched_at' => now(),
            'progress' => 100,
        ]);
    }

    public function markAsUnwatched(): void
    {
        $this->update([
            'watched' => false,
            'watched_at' => null,
            'progress' => 0,
            'watch_time' => 0,
        ]);
    }

    public function updateProgress(int $watchTime, float $progress): void
    {
        $this->update([
            'watch_time' => $watchTime,
            'progress' => $progress,
            'watched' => $progress >= 90, // Mark as watched if 90% or more is watched
            'watched_at' => $progress >= 90 ? now() : $this->watched_at,
        ]);
    }
}
