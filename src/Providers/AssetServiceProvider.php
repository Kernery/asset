<?php

namespace Kernery\Asset\Providers;

use Illuminate\Support\ServiceProvider;
use Kernery\Main\Traits\LoadAndPublishDataTrait;

class AssetServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->setNamespace('core/asset')
            ->loadAndPublishConfigs('global')
            ->loadHelpers();
    }
}
