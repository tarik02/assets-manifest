<?php

namespace Tarik02\AssetsManifest;

use Tarik02\AssetsManifest\Enums\Mode;

class Manifest
{
    /**
     * @var string
     */
    public readonly string $basePath;

    /**
     * @var string
     */
    public readonly string $baseUrl;

    /**
     * @var array
     */
    public readonly array $manifestData;

    /**
     * @var Mode|null
     */
    public readonly ?Mode $mode;

    /**
     * @var Manifest\Entrypoints
     */
    public readonly Manifest\Entrypoints $entrypoints;

    /**
     * @var Manifest\Assets
     */
    public readonly Manifest\Assets $assets;

    /**
     * @param string $basePath
     * @param string $baseUrl
     * @param array $manifestData
     */
    public function __construct(string $basePath, string $baseUrl, array $manifestData)
    {
        $this->basePath = $basePath;
        $this->baseUrl = $baseUrl;
        $this->manifestData = $manifestData;

        $this->mode = isset($manifestData['mode']) ? Mode::from($manifestData['mode']) : null;
        $this->entrypoints = new Manifest\Entrypoints($this, $manifestData['entrypoints'] ?? []);
        $this->assets = new Manifest\Assets($this, $manifestData['assets'] ?? $manifestData);
    }

    /**
     * @param string $path
     * @return string
     */
    public function url(string $path): string
    {
        return $this->assets[$path]?->url() ?? "{$this->baseUrl}{$path}";
    }

    /**
     * @param string $path
     * @return string
     */
    public function path(string $path): string
    {
        return $this->assets[$path]?->path() ?? "{$this->basePath}{$path}";
    }
}
