<?php

namespace Kernery\Asset\Supports;

use Arr;
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

    protected $assetBuilder;

    public function __construct(Repository $config, AssetBuilder $assetBuilder)
    {
        $this->config = $config->get('global');
        // $this->styles = $config['styles'];
    }

    /**
     * Append build verion for assets
     */
    public function getAssetBuildVersion(): string
    {
        $buildVersion = $this->assetBuildVersion = $this->config['allow_assets_version'] ? '?v=' . $this->config['assets_version'] : '';

        return $buildVersion;
    }

    /**
     * Add assets script
     */
    public function addScript(array | string $assets): static
    {
        $assets = array_filter($assets);

        $this->scripts = [...$this->scripts, ...$assets];

        return $this;
    }

    /**
     * Add assets style
     */
    public function addStyle(array | string $assets): static
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

            $name = 'resources.styles' . $style;

            $styles = array_merge($styles, (array) $this->getStyle($name));

        }

        return array_merge($styles, $this->appendStyles);
    }

    public function getBuilder(): AssetBuilder
    {
        return $this->assetBuilder;
    }

    public function convertStyleToHtml(string $style): string
    {
        return $this->assetBuilder->convertStyleToHtml($style);
    }

    protected function makeItemHtml(string $name, string $type = ''): string
    {
        $type = 'style';
        $html = '';
        if (! in_array($type, ['style', 'script'])) {
            return $html;
        }
        $name = 'resources.' . $type . 's.' . $name;

        if (! Arr::has($this->config, $name)) {
            return $html;
        }

        // We need to get source here
        $src = $this->getSourceUrl($name);

        $html .= $this->assetBuilder->{$type}(['class' => 'hidden'])->toHtml();

        return $html;

    }

    protected function getSourceUrl(string $configName): mixed
    {
        if (! Arr::has($this->config, $configName)) {
            return '';
        }

        if ($this->usingCDN($configName)) {
            $src = Arr::get($this->config, $configName . '.src.cdn');
        }

        $src = Arr::get($this->config, $configName . '.src.local');

        return $src;
    }

    protected function usingCDN($configName): bool
    {
        return Arr::get($this->config, $configName . '.use_cdn', false) && ! $this->config['offline_asset'];
    }
}
