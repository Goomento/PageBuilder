<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model;

use Goomento\PageBuilder\Builder\TemplateLibrary\Manager;
use Goomento\PageBuilder\Helper\StaticObjectManager;
use Goomento\PageBuilder\Api\ContentImportProcessorInterface;
use Magento\Framework\Exception\LocalizedException;


/**
 * Class ContentImportProcessor
 * @package Goomento\PageBuilder\Model
 */
class ContentImportProcessor implements ContentImportProcessorInterface
{
    /**
     * @var Manager|null
     */
    protected $documentsManager = null;

    /**
     * @return Manager|object
     */
    protected function getDocumentsManager()
    {
        if (is_null($this->documentsManager)) {
            $this->documentsManager = StaticObjectManager::get(
                Manager::class
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
