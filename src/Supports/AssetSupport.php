<?php

namespace Kernery\Asset\Supports;

use Illuminate\Config\Repository;

class AssetSupport
{
    protected array $config = [];

    protected array $styles = [];

    protected array $scripts = [];

    protected string $assetBuildVersion = '';

    protected array $appendStylesTo = [];

    protected array $appendScriptsTo = [
        'header' => [],
        'footer' => [],
    ];

    public function __construct(Repository $config)
    {
        $this->config = $config->get('global');
        // $this->styles = $config['styles'];
    }

    /**
     * Append build verion for assets
     */
    public function getAssetBuildVersion(): string
    {
        $buildVersion = $this->assetBuildVersion = $this->config['allow_assets_version'] ? '?v='.$this->config['assets_version'] : '';

        return $buildVersion;
    }

    /**
     * Add assets script
     */
    public function addScript(array|string $assets): static
    {
        $assets = array_filter($assets);

        $this->scripts = [...$this->scripts, ...$assets];

        return $this;
    }

    /**
     * Add assets style
     */
    public function addStyle(array|string $assets): static
    {
        $assets = array_filter($assets);

        $this->styles = [...$this->styles, ...$assets];

        return $this;
    }

    /**
     * Get all assets style and merge from source
     */
    public function getStyle(array $appendStyles = []): array
    {
        if (! empty($appendStyles)) {
            $this->styles = array_merge($this->styles, ...$appendStyles);
        }
        $styles = [];

        $this->styles = array_unique($this->styles);

        return $styles;

        foreach ($this->styles as $style) {

            $name = 'resources.styles'.$style;

            $styles = array_merge($styles, (array) $this->getStyle($name));

        }

        return array_merge($styles, $this->appendStyles);
    }
}
