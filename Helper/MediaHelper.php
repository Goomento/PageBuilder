<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Exception;

/**
 *
 * NOTE: Use these static methods in template hook only - which wrapped in HooksHelper::doAction( 'header' ) or
 * HooksHelper::doAction( 'footer' ) ... . Otherwise might cause some issues with classes loader.
 * See https://developer.adobe.com/commerce/php/development/components/object-manager/#usage-rules
 *
 */
// phpcs:disable Magento2.Functions.StaticFunction.StaticFunction
class MediaHelper
{
    /**
     * Contains files have been downloaded
     * @var array
     */
    private static $processed = [];

    /**
     * Download file to specific directory
     *
     * @param string $source
     * @param string $destination
     * @return string[]
     */
    public static function download(string $source, string $destination): array
    {
        $result = [
            'file' => false,
        ];

        if (isset(self::$processed[$source])) {
            return self::$processed[$source];
        }

        $paths = AssetsHelper::parsePath($destination);
        if ($paths['directory_code']) {
            if (is_file($source)) {
                AssetsHelper::copy($source, $destination);
                $result['file'] = $destination;
            } else {
                try {
                    $tmpFile = self::downloadToTmp($source);
                    AssetsHelper::copy($tmpFile, $destination);
                    $result['file'] = $destination;
                } catch (Exception $e) {
                    LoggerHelper::error($e->getMessage());
                } finally {
                    /**
                     * Set as processed
                     */
                    self::$processed[$source] = $result;

                    if (isset($tmpFile)) {
                        unlink($tmpFile);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param string $source
     * @return false|string
     */
    public static function downloadImage(string $source)
    {
        if (DataHelper::getAllowedDownloadImage()) {
            $paths = parse_url($source);
            if ($paths['path']) {
                $paths = explode('/', $paths['path']);
                $filename = array_pop($paths);
                try {
                    $file = self::download($source, DataHelper::getDownloadFolder() . $filename);
                    return !empty($file['file']) ? AssetsHelper::pathToUrl($file['file']) : false;
                } catch (Exception $e) {
                }
            }
        }

        return false;
    }

    /**
     * @param string $source
     * @return string
     * @throws Exception
     */
    private static function downloadToTmp(string $source)
    {
        $file = tempnam(sys_get_temp_dir(), 'pagebuilder_');

        $options = [
            CURLOPT_FILE    => fopen($file, 'w'),
            CURLOPT_TIMEOUT =>  30,
            CURLOPT_URL     => $source
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        curl_exec($ch);
        if (curl_errno($ch)) {
            $errorMsg = curl_error($ch);
        }
        curl_close($ch);
        if (isset($errorMsg)) {
            unlink($file);
            throw new Exception($errorMsg);
        }

        return $file;
    }
}
