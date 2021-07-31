<?php

namespace Lparede\LaravelApiKey\Providers;

use Lparede\LaravelApiKey\Console\Commands\ActivateApiKey;
use Lparede\LaravelApiKey\Console\Commands\DeactivateApiKey;
use Lparede\LaravelApiKey\Console\Commands\DeleteApiKey;
use Lparede\LaravelApiKey\Console\Commands\GenerateApiKey;
use Lparede\LaravelApiKey\Console\Commands\ListApiKeys;
use Lparede\LaravelApiKey\Http\Middleware\AuthorizeApiKey;
use Lparede\LaravelApiKey\Http\Middleware\AuthorizeApiName;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class ApiKeyServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param Router $router
     * @return void
     */
    public function boot(Router $router)
    {
        $this->registerMiddleware($router);
        $this->registerMigrations(__DIR__ . '/../../database/migrations');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() {
        $this->commands([
            ActivateApiKey::class,
            DeactivateApiKey::class,
            DeleteApiKey::class,
            GenerateApiKey::class,
            ListApiKeys::class,
        ]);
    }

    /**
     * Register middleware
     *
     * Support added for different Laravel versions
     *
     * @param Router $router
     */
    protected function registerMiddleware(Router $router)
    {
        $versionComparison = version_compare(app()->version(), '5.4.0');

        if ($versionComparison >= 0) {
			$router->aliasMiddleware('auth-api-key', AuthorizeApiKey::class);
			$router->aliasMiddleware('auth-api-name', AuthorizeApiName::class);
        } else {
			$router->middleware('auth-api-key', AuthorizeApiKey::class);
			$router->middleware('auth-api-name', AuthorizeApiKey::class);
        }
    }

    /**
     * Register migrations
     */
    protected function registerMigrations($migrationsDirectory)
    {
        $this->publishes([
            $migrationsDirectory => database_path('migrations')
        ], 'migrations');
    }
}
