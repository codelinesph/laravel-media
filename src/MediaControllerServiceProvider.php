<?php


namespace Codelines\LaravelMediaController;

use Illuminate\Support\ServiceProvider;


class MediaControllerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}