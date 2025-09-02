<?php

namespace Kernery\Asset\Supports;

use Illuminate\Config\Repository;

class AssetSupport
{
    protected array $config = [];
    protected array $styles = [];
    protected array $scripts = [];
    protected string $assetBuildVersion = "";

    public function __construct(Repository $config)
    {
        $this->config =   $config->get('asset');
        $this->styles = $config->get('asset_styles');
        $this->scripts = $config->get('asset_scripts');
    }

    /**
     * Append build verion for assets
     */
    public function getAssetBuildVersion(): string
    {
        $buildVersion = $this->assetBuildVersion = $this->config['allow_assets_version'] ? '?v=' . $this->config['assets_version'] : '';

        // return $buildVersion;

        dd( $this->assetBuildVersion );
    }


    /**
     * Get all assets style and merge from source
     */
    public function getAssetStyle(array $appendStyles = [])
    {
        $this->styles = [...array_unique([...$this->styles, ...$appendStyles])];


    }
}