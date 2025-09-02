<?php

namespace Kernery\Asset\Providers;

use Illuminate\Support\ServiceProvider;

class AssetServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . "/../../resources/views", "asset");
        $this->mergeConfigFrom(__DIR__ . "/../../config/global.php", "asset");

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . "/../../resources/views" => resource_path("views/vendor/asset")], "views");
            $this->publishes([__DIR__ . "/../../config/global" => config_path("global.php")], "config");
        }
    }
}