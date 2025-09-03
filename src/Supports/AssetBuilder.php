<?php

namespace Kernery\Asset\Supports;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\HtmlString;

class AssetBuilder
{
    protected UrlGenerator $url;

    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->url = $urlGenerator;
    }

    public function linkStyle(string $url, array $attributes = [], ?bool $isSecure = null): HtmlString
    {
        if (! $url) {
            return new HtmlString;
        }

        $defaults = [
            'media' => 'all',
            'rel' => 'stylesheet',
            'type' => 'text/css',
        ];

        $attributes = array_merge($defaults, $attributes);

        $attributes['href'] = $this->url->asset($url, $isSecure);

        return $this->toHtmlString('<link' . $this->buildAttributes($attributes) . '>');
    }

    public function linkScript(string $url, array $attributes = [], ?bool $isSecure = null): HtmlString
    {
        if (! $url) {
            return new HtmlString;
        }

        $attributes['src'] = $this->url->asset($url, $isSecure);

        return $this->toHtmlString('<script' . $this->buildAttributes($attributes) . '></script>');
    }

    protected function buildAttributeElement(string $key, array | bool | string | null $value)
    {
        if (is_bool($value) && $key !== 'value') {
            return $value ? $key : '';
        }

        if (is_array($value) && $key === 'class') {
            return 'class="' . implode(' ', $value) . '"';
        }

        if (! empty($value)) {
            return $key . '="' . e($value, false) . '"';
        }

        return $value;
    }

    public function buildAttributes(array $attributes): string
    {
        $html = [];

        foreach ((array) $attributes as $key => $value) {
            $element = is_numeric($key) ? $key : $this->buildAttributeElement($key, $value);

            if (empty($element)) {
                continue;
            }

            $html[] = $element;
        }

        return count($html) > 0 ? ' ' . implode(' ', $html) : '';
    }

    protected function toHtmlString(?string $html): HtmlString
    {
        return new HtmlString($html);
    }
}
