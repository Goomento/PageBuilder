<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model;

use Goomento\PageBuilder\Helper\Assets;
use Goomento\PageBuilder\Helper\Data;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Media
 * @package Goomento\PageBuilder\Model
 */
class Media
{
    const IMPORT_DIR = 'media/goomento/images/imported';

    /**
     * Contains files have been downloaded
     * @var array
     */
    private $processed = [];
    /**
     * @var Assets
     */
    private $assetsHelper;
    /**
     * @var Data
     */
    private $dataHelper;

    /**
     * Media constructor.
     * @param Assets $assetsHelper
     * @param Data $dataHelper
     */
    public function __construct(
        Assets $assetsHelper,
        Data $dataHelper
    )
    {
        $this->assetsHelper = $assetsHelper;
        $this->dataHelper = $dataHelper;
    }

    /**
     * @inheritdoc
     */
    public function download(string $source, string $destination): array
    {
        $result = [
            'file' => '',
        ];

        if (isset($this->processed[$source])) {
            $source = $this->processed[$source];
        }
        $paths = $this->assetsHelper->parsePath($destination);
        if ($paths['directory_code']) {
            if (is_file($source)) {
                $this->assetsHelper->copy($source, $destination);
                $result['file'] = $destination;
            } else {
                try {
                    $tmpFile = $this->downloadToTmp($source);
                    $this->validateFileType($tmpFile);
                    $this->assetsHelper->copy($tmpFile, $destination);
                    /**
                     * Set as processed
                     */
                    $this->processed[$source] = $destination;
                    $result['file'] = $destination;
                } catch (\Exception $e) {
                } finally {
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
    public function import(string $source)
    {
        if ($this->dataHelper->getAllowedDownloadImage()) {
            $paths = parse_url($source);
            if ($paths['path']) {
                $paths = explode('/', $paths['path']);
                $filename = array_pop($paths);
                try {
                    $file = $this->download($source, $this->dataHelper->getDownloadedImageFolder() . '/' . $filename);
                    return !empty($file['file']) ? $this->assetsHelper->pathToUrl($file['file']) : false;
                } catch (\Exception $e) {
                }
            }
        }

        return false;
    }

    /**
     * @param string $source
     * @return string
     * @throws \Exception
     */
    private function downloadToTmp(string $source)
    {
        $file = tempnam(sys_get_temp_dir(), 'pagebuilder_');
        $options = [
            CURLOPT_FILE    => fopen($file, 'w'),
            CURLOPT_TIMEOUT =>  15,
            CURLOPT_URL     => $source
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        curl_exec($ch);
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
        }
        curl_close($ch);
        if (isset($error_msg)) {
            unlink($file);
            throw new \Exception($error_msg);
        }

        return $file;
    }

    /**
     * @param $source
     * @return void
     * @throws LocalizedException
     */
    private function validateFileType($source)
    {
        $type = $this->getUrlMimeType($source);
        $isValid = false;
        if (!empty($type)) {
            $isValid = strpos($type, 'image/', 0) !== false;
        }

        if (!$isValid) {
            throw new LocalizedException(
                __('Invalid file type to download: %1', $type)
            );
        }
    }

    /**
     * @param $source
     * @return string
     */
    private function getUrlMimeType($source)
    {
        $buffer = file_get_contents($source);
        $info = new \finfo(FILEINFO_MIME_TYPE);
        return $info->buffer($buffer);
    }
}
