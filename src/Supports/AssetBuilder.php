<?php

namespace Kernery\Asset\Supports;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\HtmlString;


class AssetBuilder
{
    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->url = $urlGenerator;
    }
    public function linkScript(string $url, array $attributes = [], ?bool $secure_asset = null ) : string
    {
        //return empty html string if no url
        if(!$url){
            return new HtmlString();
        }

        
    }
}