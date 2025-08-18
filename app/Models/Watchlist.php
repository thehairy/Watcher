<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Watchlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'show_id',
        'movie_id',
        'status',
        'progress',
        'rating',
        'notes',
    ];

    protected $casts = [
        'rating' => 'decimal:1',
        'progress' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function show(): BelongsTo
    {
        return $this->belongsTo(Show::class);
    }

    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class);
    }

    public function getFormattedStatusAttribute(): string
    {
        return match ($this->status) {
            'watching' => 'Watching',
            'completed' => 'Completed',
            'on_hold' => 'On Hold',
            'dropped' => 'Dropped',
            'plan_to_watch' => 'Plan to Watch',
            default => ucfirst($this->status),
        };
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeShows($query)
    {
        return $query->whereNotNull('show_id');
    }

    public function scopeMovies($query)
    {
        return $query->whereNotNull('movie_id');
    }
}
