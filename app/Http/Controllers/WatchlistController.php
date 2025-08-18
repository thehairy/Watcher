<?php

namespace App\Http\Controllers;

use App\Models\Watchlist;
use App\Models\Show;
use App\Models\Movie;
use App\Models\WatchProgress;
use App\Services\TmdbService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class WatchlistController extends Controller
{
    public function __construct(
        private TmdbService $tmdbService
    ) {}

    public function index()
    {
        $user = Auth::user();
        
        // Get user's watchlist with show/movie details
        $watchlistItems = Watchlist::with(['show', 'movie'])
            ->where('user_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->get();

        // Transform watchlist items to include TMDB data
        $watchlistData = $watchlistItems->map(function ($item) use ($user) {
            $data = [
                'id' => $item->id,
                'status' => $item->status,
                'progress' => $item->progress,
                'rating' => $item->rating,
                'updated_at' => $item->updated_at,
                'type' => $item->show ? 'tv' : 'movie',
                'content' => null,
                'episodes_watched' => 0,
                'total_episodes' => 0
            ];

            try {
                if ($item->show) {
                    $tmdbData = $this->tmdbService->getTvShow($item->show->tmdb_id, $user->country);
                    
                    // Get watch providers separately for the user's country
                    $watchProviders = $this->tmdbService->getTvWatchProviders($item->show->tmdb_id, $user->country ?? 'US');
                    
                    // Calculate episode progress for TV shows using TMDB IDs
                    $watchedEpisodes = WatchProgress::where('user_id', $user->id)
                        ->where('tmdb_show_id', $item->show->tmdb_id)
                        ->where('watched', true)
                        ->count();
                    
                    // Get total episodes and filter only released episodes
                    $totalEpisodes = 0;
                    if (isset($tmdbData['seasons'])) {
                        foreach ($tmdbData['seasons'] as $season) {
                            if ($season['season_number'] > 0) {
                                try {
                                    $seasonData = $this->tmdbService->getTvSeason($item->show->tmdb_id, $season['season_number']);
                                    if (isset($seasonData['episodes'])) {
                                        foreach ($seasonData['episodes'] as $episode) {
                                            // Only count released episodes
                                            if (!empty($episode['air_date'])) {
                                                $airDate = new \DateTime($episode['air_date']);
                                                $today = new \DateTime();
                                                $today->setTime(0, 0, 0);
                                                
                                                if ($airDate <= $today) {
                                                    $totalEpisodes++;
                                                }
                                            } else {
                                                // If no air date, assume it's released
                                                $totalEpisodes++;
                                            }
                                        }
                                    }
                                } catch (\Exception $e) {
                                    // Fallback to episode count from main show data if season fetch fails
                                    $totalEpisodes = $tmdbData['number_of_episodes'] ?? 0;
                                    break;
                                }
                            }
                        }
                    } else {
                        $totalEpisodes = $tmdbData['number_of_episodes'] ?? 0;
                    }
                    
                    $data['episodes_watched'] = $watchedEpisodes;
                    $data['total_episodes'] = $totalEpisodes;
                    
                    // Calculate progress percentage for TV shows
                    if ($totalEpisodes > 0) {
                        $data['progress'] = round(($watchedEpisodes / $totalEpisodes) * 100, 1);
                    }
                    
                    $data['content'] = [
                        'id' => $item->show->tmdb_id,
                        'title' => $tmdbData['name'],
                        'overview' => $tmdbData['overview'],
                        'poster_path' => $tmdbData['poster_path'],
                        'backdrop_path' => $tmdbData['backdrop_path'],
                        'vote_average' => $tmdbData['vote_average'],
                        'first_air_date' => $tmdbData['first_air_date'],
                        'number_of_seasons' => $tmdbData['number_of_seasons'],
                        'number_of_episodes' => $tmdbData['number_of_episodes'],
                        'status' => $tmdbData['status'],
                        'genres' => $tmdbData['genres'],
                        'watch/providers' => [
                            'results' => [
                                $user->country ?? 'US' => $watchProviders
                            ]
                        ],
                    ];
                } elseif ($item->movie) {
                    $tmdbData = $this->tmdbService->getMovie($item->movie->tmdb_id, $user->country);
                    
                    // Get watch providers separately for the user's country
                    $watchProviders = $this->tmdbService->getMovieWatchProviders($item->movie->tmdb_id, $user->country ?? 'US');
                    
                    // For movies, progress is based on status
                    if ($item->status === 'completed') {
                        $data['progress'] = 100;
                    } elseif ($item->status === 'watching') {
                        $data['progress'] = 50; // Default for watching movies
                    }
                    
                    $data['content'] = [
                        'id' => $item->movie->tmdb_id,
                        'title' => $tmdbData['title'],
                        'overview' => $tmdbData['overview'],
                        'poster_path' => $tmdbData['poster_path'],
                        'backdrop_path' => $tmdbData['backdrop_path'],
                        'vote_average' => $tmdbData['vote_average'],
                        'release_date' => $tmdbData['release_date'],
                        'runtime' => $tmdbData['runtime'],
                        'genres' => $tmdbData['genres'],
                        'watch/providers' => [
                            'results' => [
                                $user->country ?? 'US' => $watchProviders
                            ]
                        ],
                    ];
                }
            } catch (\Exception $e) {
                // Log error but continue with local data
                \Log::error('Failed to fetch TMDB data for watchlist item', [
                    'item_id' => $item->id,
                    'error' => $e->getMessage()
                ]);
            }

            return $data;
        });

        return Inertia::render('Watchlist', [
            'watchlist' => $watchlistData
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'tmdb_id' => 'required|integer',
            'type' => 'required|in:movie,tv',
            'status' => 'required|in:watching,completed,plan_to_watch,on_hold,dropped'
        ]);

        $user = Auth::user();

        // Check if already in watchlist
        $existingQuery = Watchlist::where('user_id', $user->id);
        
        if ($request->type === 'tv') {
            $existingQuery->whereHas('show', function ($query) use ($request) {
                $query->where('tmdb_id', $request->tmdb_id);
            });
        } else {
            $existingQuery->whereHas('movie', function ($query) use ($request) {
                $query->where('tmdb_id', $request->tmdb_id);
            });
        }

        if ($existingQuery->exists()) {
            return response()->json(['message' => 'Already in watchlist'], 409);
        }

        try {
            // Create or find the show/movie record with TMDB data
            if ($request->type === 'tv') {
                // Get TV show details from TMDB first
                $tmdbData = $this->tmdbService->getTvShow($request->tmdb_id, $user->country);
                
                $show = Show::firstOrCreate(
                    ['tmdb_id' => $request->tmdb_id],
                    [
                        'tmdb_id' => $request->tmdb_id,
                        'name' => $tmdbData['name'],
                        'overview' => $tmdbData['overview'],
                        'poster_path' => $tmdbData['poster_path'],
                        'backdrop_path' => $tmdbData['backdrop_path'],
                        'first_air_date' => $tmdbData['first_air_date'],
                        'last_air_date' => $tmdbData['last_air_date'] ?? null,
                        'status' => $tmdbData['status'],
                        'vote_average' => $tmdbData['vote_average'],
                        'vote_count' => $tmdbData['vote_count'],
                        'number_of_seasons' => $tmdbData['number_of_seasons'],
                        'number_of_episodes' => $tmdbData['number_of_episodes'],
                        'genres' => $tmdbData['genres'] ?? [],
                        'original_language' => $tmdbData['original_language'],
                        'original_name' => $tmdbData['original_name'],
                        'tagline' => $tmdbData['tagline'] ?? null,
                    ]
                );
                
                Watchlist::create([
                    'user_id' => $user->id,
                    'show_id' => $show->id,
                    'status' => $request->status,
                    'progress' => 0
                ]);
            } else {
                // Get movie details from TMDB first
                $tmdbData = $this->tmdbService->getMovie($request->tmdb_id, $user->country);
                
                $movie = Movie::firstOrCreate(
                    ['tmdb_id' => $request->tmdb_id],
                    [
                        'tmdb_id' => $request->tmdb_id,
                        'title' => $tmdbData['title'],
                        'overview' => $tmdbData['overview'],
                        'poster_path' => $tmdbData['poster_path'],
                        'backdrop_path' => $tmdbData['backdrop_path'],
                        'release_date' => $tmdbData['release_date'],
                        'runtime' => $tmdbData['runtime'],
                        'vote_average' => $tmdbData['vote_average'],
                        'vote_count' => $tmdbData['vote_count'],
                        'genres' => $tmdbData['genres'] ?? [],
                        'original_language' => $tmdbData['original_language'],
                        'original_title' => $tmdbData['original_title'],
                        'tagline' => $tmdbData['tagline'] ?? null,
                    ]
                );
                
                Watchlist::create([
                    'user_id' => $user->id,
                    'movie_id' => $movie->id,
                    'status' => $request->status,
                    'progress' => 0
                ]);
            }

            return response()->json(['message' => 'Added to watchlist successfully']);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['message' => 'Failed to add to watchlist'], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:watching,completed,plan_to_watch,on_hold,dropped'
        ]);

        $watchlistItem = Watchlist::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $watchlistItem->update(['status' => $request->status]);

        return response()->json(['message' => 'Status updated successfully']);
    }

    public function updateProgress(Request $request, $id)
    {
        $request->validate([
            'progress' => 'required|integer|min:0|max:100'
        ]);

        $watchlistItem = Watchlist::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $watchlistItem->update(['progress' => $request->progress]);

        return response()->json(['message' => 'Progress updated successfully']);
    }

    public function remove($id)
    {
        $watchlistItem = Watchlist::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $watchlistItem->delete();

        return response()->json(['message' => 'Removed from watchlist successfully']);
    }
}
