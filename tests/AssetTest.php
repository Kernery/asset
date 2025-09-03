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
}
