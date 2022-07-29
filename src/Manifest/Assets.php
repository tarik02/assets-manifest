<?php

namespace Tarik02\AssetsManifest\Manifest;

use Tarik02\AssetsManifest\Manifest;
use ArrayAccess;
use Tarik02\AssetsManifest\Traits\ImmutableArrayAccess;

class Assets implements ArrayAccess
{
    use ImmutableArrayAccess;

    /**
     * @var Manifest
     */
    public readonly Manifest $manifest;

    /**
     * @var array<string, string>
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
     * @param string $path
     * @return string|null
     */
    public function url(string $path): ?string
    {
        return $this[$path]?->url();
    }

    /**
     * @param string $path
     * @return string|null
     */
    public function path(string $path): ?string
    {
        return $this[$path]?->path();
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return \array_key_exists($offset, $this->data);
    }

    /**
     * @param mixed $offset
     * @return Asset|null
     */
    public function offsetGet(mixed $offset): ?Asset
    {
        return \array_key_exists($offset, $this->data) ?
            new Asset($this->manifest, $this->data[$offset], $offset) :
            null;
    }
}
