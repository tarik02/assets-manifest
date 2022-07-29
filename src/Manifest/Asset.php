<?php

namespace Tarik02\AssetsManifest\Manifest;

use Tarik02\AssetsManifest\Manifest;

class Asset
{
    /**
     * @var Manifest
     */
    public readonly Manifest $manifest;

    /**
     * @var string|null
     */
    public readonly ?string $originalPath;

    /**
     * @var string
     */
    public readonly string $mappedPath;

    /**
     * @param Manifest $manifest
     * @param string $mappedPath
     * @param string|null $originalPath
     */
    public function __construct(Manifest $manifest, string $mappedPath, ?string $originalPath = null)
    {
        $this->manifest = $manifest;
        $this->mappedPath = $mappedPath;
        $this->originalPath = $originalPath;
    }

    /**
     * @return string
     */
    public function url(): string
    {
        return "{$this->manifest->baseUrl}{$this->mappedPath}";
    }

    /**
     * @return string
     */
    public function path(): string
    {
        return "{$this->manifest->basePath}{$this->mappedPath}";
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->url();
    }
}
