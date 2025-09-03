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
        $this->styles = $config['styles'];
        $this->scripts = $config['scripts'];
        $this->assetBuilder = $assetBuilder;
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
    public function fetchStyle(array $appendStyles = []): array
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

    /**
     * Get all assets script and merge from source
     */
    public function fetchScripts(?string $location = null): array
    {
        $scripts = [];

        $this->scripts = array_unique($this->scripts);

        foreach ($this->scripts as $script) {
            $config = 'resources.scripts.' . $script;

            if (! empty($location) && $location !== Arr::get($this->config, $config . '.location')) {
                continue;
            }

            $scripts = array_merge($scripts, $this->getScriptItem($location, $config, $script));
        }

        return array_merge($scripts, Arr::get($this->appendScriptsTo, $location, []));
    }

    public function getBuilder(): AssetBuilder
    {
        return $this->assetBuilder;
    }

    public function convertStyleToHtml(string $name): string
    {
        return $this->makeItemHtml($name);
    }

    public function convertScriptToHtml(string $name): string
    {
        return $this->makeItemHtml($name, 'script');
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

    protected function getSource(string $configName, ?string $location = null): array
    {
        $isUsingCdn = $this->usingCDN($configName);

        $attributes = $isUsingCdn ? [] : Arr::get($this->config, $configName . '.attributes', []);

        $src = $this->getSourceUrl($configName);

        $scripts = [];

        foreach ((array) $src as $s) {
            if (! $s) {
                continue;
            }

            $scripts[] = [
                'src' => $s,
                'attributes' => $attributes,
            ];
        }

        if (
            empty($src) &&
            $isUsingCdn &&
            $location === ASSET_SCRIPTS_HEADER &&
            Arr::has($this->config, $configName . '.fallback')
        ) {
            $scripts[] = [
                'src' => $src,
                'fallback' => Arr::get($this->config, $configName . '.fallback'),
                'fallbackURL' => Arr::get($this->config, $configName . '.src.local'),
            ];
        }

        return $scripts;
    }

    protected function getScriptItem(string $location, string $configName, string $script): array
    {
        $scripts = $this->getSource($configName, $location);

        if (Arr::get($this->config, $configName . '.include_style')) {
            $this->addStyle([$script]);
        }

        return $scripts;
    }

    public function styleToHtml(string $name): ?string
    {
        return $this->makeItemHtml($name);
    }

    public function scriptToHtml(string $name): ?string
    {
        return $this->makeItemHtml($name, 'script');
    }

    public function removeItemDirectly($assets, ?string $location = null): self
    {
        foreach ((array) $assets as $item) {
            $item = ltrim(trim($item), '/');

            if ($location && in_array($location, [ASSET_SCRIPTS_HEADER, ASSET_SCRIPTS_FOOTER])) {
                Arr::forget($this->appendedScriptsTo[$location], $item);
            } else {
                Arr::forget($this->appendedScriptsTo[ASSET_SCRIPTS_HEADER], $item);
                Arr::forget($this->appendedScriptsTo[ASSET_SCRIPTS_FOOTER], $item);
            }
        }

        return $this;
    }

    public function removeScripts($assets): self
    {
        if (empty($this->scripts)) {
            return $this;
        }

        foreach ((array) $assets as $rem) {
            $index = array_search($rem, $this->scripts);
            if ($index === false) {
                continue;
            }

            Arr::forget($this->scripts, $index);
        }

        return $this;
    }

    public function removeStyles($assets): self
    {
        if (empty($this->styles)) {
            return $this;
        }

        foreach ((array) $assets as $rem) {
            $index = array_search($rem, $this->styles);
            if ($index === false) {
                continue;
            }

            Arr::forget($this->styles, $index);
        }

        return $this;
    }

    public function addScriptsDirectly($assets, string $location = ASSET_SCRIPTS_FOOTER): self
    {
        foreach ((array) $assets as &$item) {
            $item = ltrim(trim($item), '/');

            if (! in_array($item, $this->appendScriptsTo[$location])) {
                $this->appendScriptsTo[$location][$item] = [
                    'src' => $item,
                    'attributes' => [],
                ];
            }
        }

        return $this;
    }

    public function addStylesDirectly($assets): self
    {
        foreach ((array) $assets as &$item) {
            $item = ltrim(trim($item), '/');

            if (! in_array($item, $this->appendStylesTo)) {
                $this->appendStylesTo[$item] = [
                    'src' => $item,
                    'attributes' => [],
                ];
            }
        }

        return $this;
    }

    public function renderToHeader(array $lastStyles = []): string
    {
        $renderHeaderStyles = $this->fetchStyle($lastStyles);

        $renderHeaderScripts = $this->fetchScripts(ASSET_SCRIPTS_HEADER);

        return view('asset::header', compact('renderHeaderStyles', 'renderHeaderScripts'))->render();
    }

    public function renderFooter(): string
    {
        $renderScripts = $this->fetchScripts(ASSET_SCRIPTS_FOOTER);

        return view('asset::footer', compact('renderScripts'))->render();
    }
}
