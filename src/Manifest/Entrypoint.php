<?php

namespace Tarik02\AssetsManifest\Manifest;

use Tarik02\AssetsManifest\Manifest;

class Entrypoint
{
    /**
     * @var Manifest
     */
    public readonly Manifest $manifest;

    /**
     * @var array
     */
    public readonly array $data;

    /**
     * @param Manifest $manifest
     * @param array<string, string> $data
     */
    public function __construct(Manifest $manifest, array $data)
    {
        $this->manifest = $manifest;
        $this->data = $data;
    }

    /**
     * @return Asset[]
     */
    public function js(): array
    {
        return \array_map(
            fn (string $path) => new Asset($this->manifest, mappedPath: $path, originalPath: $path),
            $this->data['assets']['js'] ?? []
        );
    }

    /**
     * @return Asset[]
     */
    public function css(): array
    {
        return \array_map(
            fn (string $path) => new Asset($this->manifest, mappedPath: $path, originalPath: $path),
            $this->data['assets']['css'] ?? []
        );
    }

    /**
     * @return string
     */
    public function code(): string
    {
        return $this->cssCode() . $this->jsCode();
    }

    /**
     * @return string
     */
    public function jsCode(): string
    {
        return \implode('', \array_map(
            fn (Asset $asset) => \sprintf('<script src="%s"></script>', \htmlspecialchars($asset->url())),
            $this->js()
        ));
    }

    /**
     * @return string
     */
    public function cssCode(): string
    {
        return \implode('', \array_map(
            fn (Asset $asset) => \sprintf('<link rel="stylesheet" href="%s">', \htmlspecialchars($asset->url())),
            $this->css()
        ));
    }
}
