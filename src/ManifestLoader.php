<?php

namespace Tarik02\AssetsManifest;

use Exception;
use Str;

use Tarik02\AssetsManifest\Exceptions\{
    HotManifestLoadingException,
    ManifestLoadingException
};

class ManifestLoader
{
    /**
     * @var string
     */
    protected string $basePath;

    /**
     * @var string
     */
    protected string $manifestBasePath;

    /**
     * @var string
     */
    protected string $manifestPath;

    /**
     * @var string
     */
    protected string $hotManifestName = 'hot.json';

    /**
     * @param string $basePath
     * @param string $manifestPath
     * @return static
     */
    public static function from(string $basePath, string $manifestPath = 'manifest.json'): self
    {
        return new ManifestLoader($basePath, $manifestPath);
    }

    /**
     * @param string $basePath
     * @param string $manifestPath
     */
    public function __construct(string $basePath, string $manifestPath = 'manifest.json')
    {
        $basePath = Utils::normalizePath($basePath);
        $this->basePath = $basePath;
        $this->manifestBasePath = $basePath;
        $this->manifestPath = $manifestPath;
    }

    /**
     * @param string $manifestBasePath
     * @return $this
     */
    public function manifestFrom(string $manifestBasePath): self
    {
        $this->manifestBasePath = Utils::normalizePath($manifestBasePath);
        return $this;
    }

    /**
     * @param string $hotManifestName
     * @return $this
     */
    public function hotManifestName(string $hotManifestName): self
    {
        $this->hotManifestName = $hotManifestName;
        return $this;
    }

    /**
     * @param string $baseUrl
     * @return Manifest
     * @throws ManifestLoadingException
     * @throws HotManifestLoadingException
     */
    public function load(string $baseUrl): Manifest
    {
        $baseUrl = Utils::normalizePath($baseUrl);

        if (Str::startsWith($this->hotManifestName, '/')) {
            $hotManifestPath = $this->hotManifestName;
        } else {
            $hotManifestPath = "{$this->manifestBasePath}{$this->hotManifestName}";
        }
        if (\file_exists($hotManifestPath)) {
            try {
                $hotManifestData = \json_decode(
                    \file_get_contents($hotManifestPath),
                    true,
                    flags: JSON_THROW_ON_ERROR
                );

                $baseUrl = $hotManifestData['baseUri'] ?? $hotManifestData['baseUrl'];

                $manifestSource = Utils::request("{$baseUrl}{$this->manifestPath}");
            } catch (Exception $exception) {
                throw new HotManifestLoadingException(previous: $exception);
            }
        }

        try {
            if (!isset($manifestSource)) {
                $manifestSource = \file_get_contents("{$this->manifestBasePath}{$this->manifestPath}");
            }

            $manifestData = \json_decode(
                $manifestSource,
                true,
                flags: JSON_THROW_ON_ERROR
            );

            return new Manifest(
                $this->basePath,
                $baseUrl,
                $manifestData
            );
        } catch (Exception $exception) {
            throw new ManifestLoadingException(previous: $exception);
        }
    }
}
