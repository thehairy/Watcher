<?php

use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\WatchlistController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DiscoverController;
use App\Http\Controllers\ShowController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Main app routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Watchlist routes
    Route::get('/watchlist', [WatchlistController::class, 'index'])->name('watchlist');
    Route::post('/watchlist/add', [WatchlistController::class, 'add'])->name('watchlist.add');
    Route::patch('/watchlist/{id}/status', [WatchlistController::class, 'updateStatus'])->name('watchlist.update-status');
    Route::patch('/watchlist/{id}/progress', [WatchlistController::class, 'updateProgress'])->name('watchlist.update-progress');
    Route::delete('/watchlist/{id}', [WatchlistController::class, 'remove'])->name('watchlist.remove');
    
    // Calendar routes
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
    Route::get('/calendar/upcoming', [CalendarController::class, 'getUpcoming'])->name('calendar.upcoming');
    
    // Discover routes
    Route::get('/discover', [DiscoverController::class, 'index'])->name('discover');
    Route::get('/discover/search', [DiscoverController::class, 'search'])->name('discover.search');
    Route::get('/discover/trending', [DiscoverController::class, 'trending'])->name('discover.trending');
    Route::get('/discover/popular/movies', [DiscoverController::class, 'popularMovies'])->name('discover.popular.movies');
    Route::get('/discover/popular/tv', [DiscoverController::class, 'popularTvShows'])->name('discover.popular.tv');
    Route::get('/discover/content', [DiscoverController::class, 'getContent'])->name('discover.content');
    
    // Media API routes
    Route::prefix('api')->group(function () {
        Route::get('/media/tv/{id}', [ShowController::class, 'getTvShow'])->name('api.media.tv');
        Route::get('/media/movie/{id}', [ShowController::class, 'getMovie'])->name('api.media.movie');
        Route::get('/tv/{id}/season/{season}', [ShowController::class, 'getSeason'])->name('api.tv.season');
        Route::get('/tv/{id}/season/{season}/episode/{episode}', [ShowController::class, 'getEpisode'])->name('api.tv.episode');
        Route::get('/watch-progress/show/{id}', [ShowController::class, 'getWatchProgress'])->name('api.watch-progress.show');
        Route::post('/watch-progress/episode', [ShowController::class, 'toggleEpisodeWatched'])->name('api.watch-progress.episode');
        Route::post('/watch-progress/season', [ShowController::class, 'toggleSeasonWatched'])->name('api.watch-progress.season');
    });
});

// Social authentication routes
Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
