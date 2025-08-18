<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TmdbService
{
    private Client $client;
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = config('services.tmdb.api_key');
        $this->baseUrl = config('services.tmdb.base_url');
    }

    /**
     * Search for movies and TV shows
     */
    public function search(string $query, int $page = 1): array
    {
        return $this->makeRequest('search/multi', [
            'query' => $query,
            'page' => $page,
            'include_adult' => false
        ]);
    }

    /**
     * Get popular TV shows
     */
    public function getPopularTvShows(int $page = 1): array
    {
        return $this->makeRequest('tv/popular', [
            'page' => $page,
            'language' => 'en-US'
        ]);
    }

    /**
     * Get popular movies
     */
    public function getPopularMovies(int $page = 1): array
    {
        return $this->makeRequest('movie/popular', [
            'page' => $page,
            'language' => 'en-US'
        ]);
    }

    /**
     * Get TV show details
     */
    public function getTvShow(int $id): array
    {
        return $this->makeRequest("tv/{$id}", [
            'language' => 'en-US',
            'append_to_response' => 'credits,videos,external_ids,watch/providers'
        ]);
    }

    /**
     * Get movie details
     */
    public function getMovie(int $id): array
    {
        return $this->makeRequest("movie/{$id}", [
            'language' => 'en-US',
            'append_to_response' => 'credits,videos'
        ]);
    }

    /**
     * Get TV show season details
     */
    public function getTvSeason(int $showId, int $seasonNumber): array
    {
        return $this->makeRequest("tv/{$showId}/season/{$seasonNumber}", [
            'language' => 'en-US'
        ]);
    }

    /**
     * Get TV show episode details
     */
    public function getTvEpisode(int $showId, int $seasonNumber, int $episodeNumber): array
    {
        return $this->makeRequest("tv/{$showId}/season/{$seasonNumber}/episode/{$episodeNumber}", [
            'language' => 'en-US'
        ]);
    }

    /**
     * Get upcoming movies
     */
    public function getUpcomingMovies(int $page = 1): array
    {
        return $this->makeRequest('movie/upcoming', [
            'page' => $page,
            'language' => 'en-US',
            'region' => 'US'
        ]);
    }

    /**
     * Get TV shows airing today
     */
    public function getTvAiringToday(int $page = 1): array
    {
        return $this->makeRequest('tv/airing_today', [
            'page' => $page,
            'language' => 'en-US'
        ]);
    }

    /**
     * Get TV shows on the air (currently running)
     */
    public function getTvOnTheAir(int $page = 1): array
    {
        return $this->makeRequest('tv/on_the_air', [
            'page' => $page,
            'language' => 'en-US'
        ]);
    }

    /**
     * Get trending content
     */
    public function getTrending(string $mediaType = 'all', string $timeWindow = 'week'): array
    {
        return $this->makeRequest("trending/{$mediaType}/{$timeWindow}", [
            'language' => 'en-US'
        ]);
    }

    /**
     * Get image configuration
     */
    public function getConfiguration(): array
    {
        return Cache::remember('tmdb_configuration', 86400, function () {
            return $this->makeRequest('configuration');
        });
    }

    /**
     * Build full image URL
     */
    public function getImageUrl(string $path, string $size = 'w500'): string
    {
        $config = $this->getConfiguration();
        $baseUrl = $config['images']['secure_base_url'] ?? 'https://image.tmdb.org/t/p/';
        
        return $baseUrl . $size . $path;
    }

    /**
     * Make API request to TMDB
     */
    private function makeRequest(string $endpoint, array $params = []): array
    {
        try {
            $params['api_key'] = $this->apiKey;
            
            $response = $this->client->get($this->baseUrl . '/' . $endpoint, [
                'query' => $params,
                'timeout' => 30
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('TMDB API request failed', [
                'endpoint' => $endpoint,
                'params' => $params,
                'error' => $e->getMessage()
            ]);

            throw new \Exception('Failed to fetch data from TMDB API: ' . $e->getMessage());
        }
    }
}
