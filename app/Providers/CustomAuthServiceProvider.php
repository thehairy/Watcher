<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class CustomAuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        \Auth::provider('custom_eloquent', function ($app, array $config) {
            return new class($app['hash'], $config['model']) extends EloquentUserProvider {
                public function retrieveByCredentials(array $credentials)
                {
                    if (empty($credentials) ||
                        (count($credentials) === 1 &&
                         array_key_exists('password', $credentials))) {
                        return;
                    }

                    // Build the query for the user
                    $query = $this->createModel()->newQuery();

                    // If credentials contain email or username, search by both
                    if (isset($credentials['email'])) {
                        $query->where(function ($q) use ($credentials) {
                            $q->where('email', $credentials['email'])
                              ->orWhere('username', $credentials['email']);
                        });
                    }

                    foreach ($credentials as $key => $value) {
                        if ($key !== 'password' && $key !== 'email') {
                            $query->where($key, $value);
                        }
                    }

                    return $query->first();
                }
            };
        });
    }
}
