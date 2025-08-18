<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'discord_id',
        'github_id',
        'steam_id',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function watchlists()
    {
        return $this->hasMany(Watchlist::class);
    }

    public function watchProgress()
    {
        return $this->hasMany(WatchProgress::class);
    }

    public function watchingShows()
    {
        return $this->watchlists()->shows()->byStatus('watching');
    }

    public function watchingMovies()
    {
        return $this->watchlists()->movies()->byStatus('watching');
    }

    public function completedShows()
    {
        return $this->watchlists()->shows()->byStatus('completed');
    }

    public function completedMovies()
    {
        return $this->watchlists()->movies()->byStatus('completed');
    }
}
