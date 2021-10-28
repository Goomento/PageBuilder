<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Api;

use Magento\Framework\Exception\LocalizedException;

interface ContentImportProcessorInterface
{
    /**
     * @param $filename
     * @return array|int
     * @throws LocalizedException
     */
    public function importOnUpload($filename);

    /**
     * @param $path
     * @return array|int
     * @throws LocalizedException
     */
    public function importByPath($path);

    /**
     * @param string $content
     * @return array|int
     * @throws LocalizedException
     */
    public function import(string $content);
}
