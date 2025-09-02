<?php

namespace Kernery\Asset\Facades;

use Illuminate\Support\Facades\Facade;
use Kernery\Asset\Supports\AssetSupport as Asset;

class AssetFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Asset::class;
    }
}