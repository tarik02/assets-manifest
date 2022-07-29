<?php

namespace Tarik02\AssetsManifest\Manifest;

use ArrayAccess;

use Tarik02\AssetsManifest\{
    Traits\ImmutableArrayAccess,
    Manifest
};

class Entrypoints implements ArrayAccess
{
    use ImmutableArrayAccess;

    /**
     * @var Manifest
     */
    protected Manifest $manifest;

    /**
     * @var array<string, array>
     */
    protected array $data;

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
     * @param string ...$entrypointNames
     * @return Entrypoint
     */
    public function include(string ...$entrypointNames): Entrypoint
    {
        $entrypointList = \array_map(
            fn (string $entrypointName) => $this->data[$entrypointName],
            $entrypointNames
        );

        return new Entrypoint(
            $this->manifest,
            [
                'assets' => [
                    'js' => \array_unique(
                        \array_merge(
                            ...\array_map(
                                fn (array $entrypointData) => $entrypointData['assets']['js'] ?? [],
                                $entrypointList
                            )
                        )
                    ),
                    'css' => \array_unique(
                        \array_merge(
                            ...\array_map(
                                fn (array $entrypointData) => $entrypointData['assets']['css'] ?? [],
                                $entrypointList
                            )
                        )
                    ),
                ],
            ]
        );
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
     * @return Entrypoint|null
     */
    public function offsetGet(mixed $offset): ?Entrypoint
    {
        return \array_key_exists($offset, $this->data) ?
            new Entrypoint($this->manifest, $this->data[$offset]) :
            null;
    }
}
