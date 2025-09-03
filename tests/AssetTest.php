<?php

namespace Kernery\Asset\Tests;

use Kernery\Asset\Facades\Asset;
use Tests\TestCase;

class AssetTest extends TestCase
{
    public function test_that_it_returns_build_version_if_asset_version_enabled()
    {
        config()->set('global', [
            'allow_assets_version' => true,
            'assets_version' => '12345',
        ]);
        $this->assertSame('?v=12345', Asset::getAssetBuildVersion());
    }

    public function test_that_it_appends_style_with_empty_object()
    {

        config()->set('global.styles', []);

        $result = Asset::getStyle([
            ['/css/app.css', '/css/theme.css'],
        ]);

        $this->assertSame([], $result);
    }
}
