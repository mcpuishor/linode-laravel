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
