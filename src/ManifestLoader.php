<?php

namespace Tarik02\AssetsManifest;

use Exception;
use Str;

use Tarik02\AssetsManifest\Exceptions\{
    CurlException,
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
        $basePath = $this->basePath;

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

                $manifestBaseUrl = $hotManifestData['baseUri'] ?? $hotManifestData['baseUrl'];
                $manifestBasePath = $manifestBaseUrl;

                $manifestUrl = isset($hotManifestData['socket'])
                    ? \preg_replace('~^https:~', 'http:', $manifestBaseUrl) . $this->manifestPath
                    : $manifestBaseUrl . $this->manifestPath;

                $manifestSource = Utils::request(
                    $manifestUrl,
                    [
                        \CURLOPT_UNIX_SOCKET_PATH => isset($hotManifestData['socket'])
                            ? \sprintf(
                                '%s/%s',
                                \dirname($hotManifestPath),
                                $hotManifestData['socket']
                            )
                            : null,
                    ]
                );

                $baseUrl = $manifestBaseUrl;
                $basePath = $manifestBasePath;
            } catch (Exception $exception) {
                if (
                    $exception instanceof CurlException
                        && isset($hotManifestData['socket'])
                        && $exception->getCode() === \CURLE_COULDNT_CONNECT
                ) {
                    // Socket file exists, but CURL can't connect to it
                } else {
                    throw new HotManifestLoadingException(previous: $exception);
                }
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
                $basePath,
                $baseUrl,
                $manifestData
            );
        } catch (Exception $exception) {
            throw new ManifestLoadingException(previous: $exception);
        }
    }
}
