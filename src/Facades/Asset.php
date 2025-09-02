<?php

namespace Kernery\Asset\Facades;

use Illuminate\Support\Facades\Facade;
use Kernery\Asset\Supports\AssetSupport as AssetSupport;

class Asset extends Facade
{
    protected static function getFacadeAccessor()
    {
        return AssetSupport::class;
    }
}