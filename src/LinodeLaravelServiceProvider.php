<?php

namespace Mcpuishor\LinodeLaravel;

use Illuminate\Support\ServiceProvider;

class LinodeLaravelServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__.'/../config/linode.php', 'linode'
        );

        // Register Transport
        $this->app->singleton(Transport::class, function ($app) {
            return new Transport();
        });

        // Register LinodeClient#
        $this->app->singleton(LinodeClient::class, function ($app) {
            return new LinodeClient(
                app()->make(Transport::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__.'/../config/linode.php' => config_path('linode.php'),
        ], 'linode-config');
    }
}
