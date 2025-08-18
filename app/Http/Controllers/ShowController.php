<?php

namespace App\Http\Controllers;

use App\Models\Show;
use App\Models\Episode;
use App\Models\WatchProgress;
use App\Services\TmdbService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ShowController extends Controller
{
    protected TmdbService $tmdbService;

    public function __construct(TmdbService $tmdbService)
    {
        $this->tmdbService = $tmdbService;
    }

    /**
     * Get TV show details from TMDB
     */
    public function getTvShow($id)
    {
        try {
            $showData = $this->tmdbService->getTvShow($id);
            return response()->json($showData);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch show details'], 500);
        }
    }

    /**
     * Get movie details from TMDB
     */
    public function getMovie($id)
    {
        try {
            $movieData = $this->tmdbService->getMovie($id);
            return response()->json($movieData);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch movie details'], 500);
        }
    }

    /**
     * Get season details with episodes
     */
    public function getSeason($showId, $seasonNumber)
    {
        try {
            $seasonData = $this->tmdbService->getTvSeason($showId, $seasonNumber);
            return response()->json($seasonData);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch season details'], 500);
        }
    }

    /**
     * Get episode details with watch providers
     */
    public function getEpisode($showId, $seasonNumber, $episodeNumber)
    {
        try {
            // Get episode details
            $episodeData = $this->tmdbService->getTvEpisode($showId, $seasonNumber, $episodeNumber);
            
            // Get show watch providers (episodes share the same providers as the show)
            $showData = $this->tmdbService->getTvShow($showId);
            if (isset($showData['watch/providers'])) {
                $episodeData['watch_providers'] = $showData['watch/providers']['results'];
            }
            
            return response()->json($episodeData);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch episode details'], 500);
        }
    }

    /**
     * Get watch progress for a show
     */
    public function getWatchProgress($showId)
    {
        $user = Auth::user();
        
        $watchProgress = WatchProgress::where('user_id', $user->id)
            ->where('tmdb_show_id', $showId)
            ->where('watched', true)
            ->get();

        // Return the tmdb_episode_id field 
        return response()->json($watchProgress->pluck('tmdb_episode_id')->toArray());
    }

    /**
     * Toggle episode watch status
     */
    public function toggleEpisodeWatched(Request $request)
    {
        $request->validate([
            'episode_id' => 'required|integer',
            'show_id' => 'required|integer',
            'watched' => 'required|boolean'
        ]);

        $user = Auth::user();

        $watchProgress = WatchProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'tmdb_episode_id' => $request->episode_id,
                'tmdb_show_id' => $request->show_id,
            ],
            [
                'watched' => $request->watched,
                'watched_at' => $request->watched ? now() : null,
            ]
        );

        // Update watchlist progress
        $this->updateWatchlistProgress($user->id, $request->show_id);

        return response()->json(['success' => true, 'watch_progress' => $watchProgress]);
    }

    /**
     * Mark entire season as watched/unwatched
     */
    public function toggleSeasonWatched(Request $request)
    {
        $request->validate([
            'show_id' => 'required|integer',
            'season_number' => 'required|integer',
            'watched' => 'required|boolean'
        ]);

        $user = Auth::user();

        try {
            // Get all episodes for this season from TMDB
            $seasonData = $this->tmdbService->getTvSeason($request->show_id, $request->season_number);
            
            if (!isset($seasonData['episodes'])) {
                return response()->json(['error' => 'Season episodes not found'], 404);
            }

            // Update watch progress for all episodes in the season
            foreach ($seasonData['episodes'] as $episode) {
                WatchProgress::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'tmdb_episode_id' => $episode['id'],
                        'tmdb_show_id' => $request->show_id,
                    ],
                    [
                        'watched' => $request->watched,
                        'watched_at' => $request->watched ? now() : null,
                    ]
                );
            }

            // Update watchlist progress
            $this->updateWatchlistProgress($user->id, $request->show_id);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update season watch status'], 500);
        }
    }

    /**
     * Update watchlist progress based on watched episodes
     */
    private function updateWatchlistProgress($userId, $tmdbShowId)
    {
        try {
            // Find the watchlist item
            $watchlistItem = \App\Models\Watchlist::whereHas('show', function($query) use ($tmdbShowId) {
                $query->where('tmdb_id', $tmdbShowId);
            })->where('user_id', $userId)->first();

            if (!$watchlistItem) {
                return;
            }

            // Get show data from TMDB
            $showData = $this->tmdbService->getTvShow($tmdbShowId);
            
            // Calculate total released episodes
            $totalReleasedEpisodes = 0;
            if (isset($showData['seasons'])) {
                foreach ($showData['seasons'] as $season) {
                    if ($season['season_number'] > 0) {
                        try {
                            $seasonData = $this->tmdbService->getTvSeason($tmdbShowId, $season['season_number']);
                            if (isset($seasonData['episodes'])) {
                                foreach ($seasonData['episodes'] as $episode) {
                                    // Only count released episodes
                                    if (!empty($episode['air_date'])) {
                                        $airDate = new \DateTime($episode['air_date']);
                                        $today = new \DateTime();
                                        $today->setTime(0, 0, 0);
                                        
                                        if ($airDate <= $today) {
                                            $totalReleasedEpisodes++;
                                        }
                                    } else {
                                        // If no air date, assume it's released
                                        $totalReleasedEpisodes++;
                                    }
                                }
                            }
                        } catch (\Exception $e) {
                            // Fallback to total episode count if season fetch fails
                            $totalReleasedEpisodes = $showData['number_of_episodes'] ?? 0;
                            break;
                        }
                    }
                }
            } else {
                $totalReleasedEpisodes = $showData['number_of_episodes'] ?? 0;
            }

            if ($totalReleasedEpisodes > 0) {
                // Count watched episodes
                $watchedEpisodes = WatchProgress::where('user_id', $userId)
                    ->where('tmdb_show_id', $tmdbShowId)
                    ->where('watched', true)
                    ->count();

                // Calculate progress percentage
                $progress = round(($watchedEpisodes / $totalReleasedEpisodes) * 100, 1);

                // Update watchlist item
                $watchlistItem->update(['progress' => $progress]);

                // If 100% complete, update status to completed
                if ($progress >= 100 && $watchlistItem->status !== 'completed') {
                    $watchlistItem->update(['status' => 'completed']);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to update watchlist progress', [
                'user_id' => $userId,
                'tmdb_show_id' => $tmdbShowId,
                'error' => $e->getMessage()
            ]);
        }
    }
}
