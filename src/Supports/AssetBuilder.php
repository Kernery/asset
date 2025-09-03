<?php

namespace Kernery\Asset\Supports;

use Illuminate\Contracts\Routing\UrlGenerator;

class AssetBuilder
{
    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->url = $urlGenerator;
    }
}
