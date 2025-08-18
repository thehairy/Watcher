<?php

namespace App\Http\Controllers;

use App\Models\Watchlist;
use App\Services\TmdbService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function __construct(
        private TmdbService $tmdbService
    ) {}

    public function index()
    {
        $user = Auth::user();
        
        // Get user's watchlist items
        $watchlistItems = Watchlist::with(['show', 'movie'])
            ->where('user_id', $user->id)
            ->get();

        $upcomingReleases = [];
        $waitingForReleaseDate = [];
        $endedContent = [];

        foreach ($watchlistItems as $item) {
            try {
                if ($item->show) {
                    // Get TV show details
                    $tmdbData = $this->tmdbService->getTvShow($item->show->tmdb_id);
                    
                    $showData = [
                        'id' => $item->id,
                        'type' => 'tv',
                        'tmdb_id' => $item->show->tmdb_id,
                        'title' => $tmdbData['name'],
                        'poster_path' => $tmdbData['poster_path'],
                        'backdrop_path' => $tmdbData['backdrop_path'],
                        'overview' => $tmdbData['overview'],
                        'vote_average' => $tmdbData['vote_average'],
                        'status' => $tmdbData['status'],
                        'genres' => $tmdbData['genres'],
                        'networks' => $tmdbData['networks'] ?? [],
                        'first_air_date' => $tmdbData['first_air_date'],
                        'last_air_date' => $tmdbData['last_air_date'] ?? null,
                    ];

                    // Categorize shows based on status and release dates
                    if ($tmdbData['status'] === 'Ended' || $tmdbData['status'] === 'Canceled') {
                        $endedContent[] = $showData;
                    } elseif (isset($tmdbData['next_episode_to_air'])) {
                        // Has upcoming episodes
                        $showData['release_date'] = $tmdbData['next_episode_to_air']['air_date'];
                        $showData['episode_info'] = [
                            'season' => $tmdbData['next_episode_to_air']['season_number'],
                            'episode' => $tmdbData['next_episode_to_air']['episode_number'],
                            'name' => $tmdbData['next_episode_to_air']['name'],
                        ];
                        $releaseDate = Carbon::parse($showData['release_date']);
                        $now = Carbon::now();
                        $showData['days_until'] = round($now->diffInDays($releaseDate));
                        if ($releaseDate->isPast()) {
                            $showData['days_until'] = 0; // If it's past, show as today
                        }
                        $upcomingReleases[] = $showData;
                    } elseif ($tmdbData['status'] === 'Returning Series' || $tmdbData['status'] === 'In Production') {
                        // Waiting for release date
                        $waitingForReleaseDate[] = $showData;
                    } else {
                        // Unknown status, put in waiting
                        $waitingForReleaseDate[] = $showData;
                    }

                } elseif ($item->movie) {
                    // Get movie details
                    $tmdbData = $this->tmdbService->getMovie($item->movie->tmdb_id);
                    
                    $movieData = [
                        'id' => $item->id,
                        'type' => 'movie',
                        'tmdb_id' => $item->movie->tmdb_id,
                        'title' => $tmdbData['title'],
                        'poster_path' => $tmdbData['poster_path'],
                        'backdrop_path' => $tmdbData['backdrop_path'],
                        'overview' => $tmdbData['overview'],
                        'vote_average' => $tmdbData['vote_average'],
                        'runtime' => $tmdbData['runtime'],
                        'genres' => $tmdbData['genres'],
                        'status' => $tmdbData['status'] ?? 'Released',
                    ];

                    if (isset($tmdbData['release_date'])) {
                        $releaseDate = Carbon::parse($tmdbData['release_date']);
                        $movieData['release_date'] = $tmdbData['release_date'];
                        
                        if ($releaseDate->isFuture()) {
                            // Upcoming movie
                            $movieData['days_until'] = round(Carbon::now()->diffInDays($releaseDate));
                            $upcomingReleases[] = $movieData;
                        } else {
                            // Already released movie
                            $endedContent[] = $movieData;
                        }
                    } else {
                        // No release date
                        $waitingForReleaseDate[] = $movieData;
                    }
                }
            } catch (\Exception $e) {
                // Log error but continue with other items
                \Log::error('Failed to fetch TMDB data for calendar item', [
                    'item_id' => $item->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Sort upcoming releases by release date
        usort($upcomingReleases, function ($a, $b) {
            return strtotime($a['release_date']) - strtotime($b['release_date']);
        });

        // Sort ended content by last air date or release date (most recent first)
        usort($endedContent, function ($a, $b) {
            $dateA = $a['last_air_date'] ?? $a['release_date'] ?? '1900-01-01';
            $dateB = $b['last_air_date'] ?? $b['release_date'] ?? '1900-01-01';
            return strtotime($dateB) - strtotime($dateA);
        });

        return Inertia::render('Calendar', [
            'upcomingReleases' => [
                'today' => [],
                'this_week' => [],
                'this_month' => [],
                'later' => $upcomingReleases
            ],
            'waitingForReleaseDate' => $waitingForReleaseDate,
            'endedContent' => $endedContent
        ]);
    }

    public function getUpcoming()
    {
        try {
            // Get upcoming movies and TV shows
            $upcomingMovies = $this->tmdbService->getUpcomingMovies();
            $airingToday = $this->tmdbService->getTvAiringToday();
            $onTheAir = $this->tmdbService->getTvOnTheAir();

            return response()->json([
                'upcoming_movies' => $upcomingMovies['results'] ?? [],
                'airing_today' => $airingToday['results'] ?? [],
                'on_the_air' => $onTheAir['results'] ?? []
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch upcoming content'], 500);
        }
    }
}
