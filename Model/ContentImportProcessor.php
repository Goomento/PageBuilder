<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model;

use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Api\ContentImportProcessorInterface;
use Magento\Framework\Exception\LocalizedException;

class ContentImportProcessor implements ContentImportProcessorInterface
{
    /**
     * @inheritdoc
     */
    public function importOnUpload($filename)
    {
        return ObjectManagerHelper::getSourcesManager()->directImportTemplate($filename);
    }

    /**
     * @inheritdoc
     */
    public function import(string $fileData)
    {
        return ObjectManagerHelper::getSourcesManager()->importTemplate([
            'fileData' => base64_encode($fileData),
            'fileName' => ''
        ]);
    }

    /**
     * @inheritdoc
     */
    public function importByPath($path)
    {
        if (!file_exists($path)) {
            throw new LocalizedException(
                __('File does not exited: %1', $path)
            );
        }

        return $this->import(file_get_contents($path));
    }
}
