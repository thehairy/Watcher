<?php

namespace App\Http\Controllers;

use App\Services\TmdbService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DiscoverController extends Controller
{
    public function __construct(
        private TmdbService $tmdbService
    ) {}

    public function index()
    {
        try {
            $user = auth()->user();
            $region = $user?->country;
            
            // Get initial discover data
            $trending = $this->tmdbService->getTrending();
            $popularMovies = $this->tmdbService->getPopularMovies(1, $region);
            $popularTvShows = $this->tmdbService->getPopularTvShows(1, $region);

            return Inertia::render('Discover', [
                'trending' => $trending['results'] ?? [],
                'popular_movies' => $popularMovies['results'] ?? [],
                'popular_tv_shows' => $popularTvShows['results'] ?? [],
                'genres' => $this->getGenres()
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch discover data', ['error' => $e->getMessage()]);
            
            return Inertia::render('Discover', [
                'trending' => [],
                'popular_movies' => [],
                'popular_tv_shows' => [],
                'genres' => $this->getGenres()
            ]);
        }
    }

    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:1',
            'page' => 'integer|min:1'
        ]);

        try {
            $results = $this->tmdbService->search(
                $request->input('query'), 
                $request->input('page', 1)
            );

            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Search failed'], 500);
        }
    }

    public function trending(Request $request)
    {
        $request->validate([
            'media_type' => 'string|in:all,movie,tv,person',
            'time_window' => 'string|in:day,week'
        ]);

        try {
            $results = $this->tmdbService->getTrending(
                $request->media_type ?? 'all',
                $request->time_window ?? 'week'
            );

            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch trending content'], 500);
        }
    }

    public function popularMovies(Request $request)
    {
        $request->validate([
            'page' => 'integer|min:1'
        ]);

        try {
            $user = auth()->user();
            $region = $user?->country;
            
            $results = $this->tmdbService->getPopularMovies($request->page ?? 1, $region);
            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch popular movies'], 500);
        }
    }

    public function popularTvShows(Request $request)
    {
        $request->validate([
            'page' => 'integer|min:1'
        ]);

        try {
            $user = auth()->user();
            $region = $user?->country;
            
            $results = $this->tmdbService->getPopularTvShows($request->page ?? 1, $region);
            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch popular TV shows'], 500);
        }
    }

    public function getContent(Request $request)
    {
        $request->validate([
            'type' => 'required|in:movie,tv',
            'id' => 'required|integer'
        ]);

        try {
            $user = auth()->user();
            $region = $user?->country;
            
            if ($request->type === 'movie') {
                $content = $this->tmdbService->getMovie($request->id, $region);
            } else {
                $content = $this->tmdbService->getTvShow($request->id, $region);
            }

            return response()->json($content);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch content details'], 500);
        }
    }

    private function getGenres(): array
    {
        // Common genres for both movies and TV shows
        return [
            ['id' => 28, 'name' => 'Action'],
            ['id' => 12, 'name' => 'Adventure'],
            ['id' => 16, 'name' => 'Animation'],
            ['id' => 35, 'name' => 'Comedy'],
            ['id' => 80, 'name' => 'Crime'],
            ['id' => 99, 'name' => 'Documentary'],
            ['id' => 18, 'name' => 'Drama'],
            ['id' => 10751, 'name' => 'Family'],
            ['id' => 14, 'name' => 'Fantasy'],
            ['id' => 36, 'name' => 'History'],
            ['id' => 27, 'name' => 'Horror'],
            ['id' => 10402, 'name' => 'Music'],
            ['id' => 9648, 'name' => 'Mystery'],
            ['id' => 10749, 'name' => 'Romance'],
            ['id' => 878, 'name' => 'Science Fiction'],
            ['id' => 10770, 'name' => 'TV Movie'],
            ['id' => 53, 'name' => 'Thriller'],
            ['id' => 10752, 'name' => 'War'],
            ['id' => 37, 'name' => 'Western'],
            ['id' => 10759, 'name' => 'Action & Adventure'],
            ['id' => 10762, 'name' => 'Kids'],
            ['id' => 10763, 'name' => 'News'],
            ['id' => 10764, 'name' => 'Reality'],
            ['id' => 10765, 'name' => 'Sci-Fi & Fantasy'],
            ['id' => 10766, 'name' => 'Soap'],
            ['id' => 10767, 'name' => 'Talk'],
            ['id' => 10768, 'name' => 'War & Politics'],
        ];
    }
}
