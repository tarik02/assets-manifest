<?php

namespace Tarik02\AssetsManifest\Manifest;

use Tarik02\AssetsManifest\Manifest;

class Entrypoint
{
    /**
     * @param array<string, string> $data
     * @param array<int, array<string, mixed>> $original
     */
    public function __construct(
        public readonly Manifest $manifest,
        public readonly array $data,
        public readonly array $original
    ) {
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
