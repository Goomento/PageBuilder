<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model;

use Goomento\PageBuilder\Builder\Managers\Sources;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Api\ContentImportProcessorInterface;
use Magento\Framework\Exception\LocalizedException;

class ContentImportProcessor implements ContentImportProcessorInterface
{
    /**
     * @var Sources|null
     */
    protected $documentsManager = null;

    /**
     * @return Sources|object
     */
    protected function getDocumentsManager()
    {
        if (is_null($this->documentsManager)) {
            $this->documentsManager = ObjectManagerHelper::get(
                Sources::class
            );
        }
        return $this->documentsManager;
    }

    /**
     * @inheritdoc
     */
    public function importOnUpload($filename)
    {
        return $this->getDocumentsManager()
            ->directImportTemplate($filename);
    }

    /**
     * @inheritdoc
     */
    public function import(string $fileData)
    {
        return $this->getDocumentsManager()->importTemplate([
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
