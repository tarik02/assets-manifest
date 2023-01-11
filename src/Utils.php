<?php

namespace Tarik02\AssetsManifest;

use Tarik02\AssetsManifest\Exceptions\CurlException;

class Utils
{
    /**
     * @param string $url
     * @param array<int, mixed> $options
     * @return string
     * @throws CurlException
     */
    public static function request(string $url, array $options = []): string
    {
        $curl = \curl_init();
        try {
            \curl_setopt($curl, \CURLOPT_RETURNTRANSFER, 1);
            \curl_setopt($curl, \CURLOPT_URL, $url);
            \curl_setopt($curl, \CURLOPT_FAILONERROR, true);

            foreach ($options as $option => $value) {
                \curl_setopt($curl, $option, $value);
            }

            $result = \curl_exec($curl);

            if (\curl_errno($curl) !== 0) {
                throw new CurlException(\curl_error($curl), \curl_errno($curl));
            }

            return $result;
        } finally {
            \curl_close($curl);
        }
    }

    /**
     * @param string $path
     * @return string
     */
    public static function normalizePath(string $path): string
    {
        return \rtrim($path, '\\/') . '/';
    }
}
