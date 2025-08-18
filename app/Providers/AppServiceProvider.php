<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->bootSocialiteProviders();
    }

    /**
     * Bootstrap the socialite providers.
     */
    private function bootSocialiteProviders(): void
    {
        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
        
        $socialite->extend(
            'steam',
            function ($app) use ($socialite) {
                $config = $app['config']['services.steam'];
                return $socialite->buildProvider(
                    \SocialiteProviders\Steam\Provider::class,
                    $config
                );      
            }
        );
        
        $socialite->extend(
            'discord',
            function ($app) use ($socialite) {
                $config = $app['config']['services.discord'];
                return $socialite->buildProvider(
                    \SocialiteProviders\Discord\Provider::class,
                    $config
                );      
            }
        );
    }
}
