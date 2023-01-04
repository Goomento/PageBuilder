<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\DocumentTypes;

use Goomento\PageBuilder\Builder\Base\AbstractDocumentType;

// phpcs:disable Magento2.Functions.StaticFunction.StaticFunction
class Page extends AbstractDocumentType
{
    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        parent::registerControls();
        Section::registerLayoutControl($this, 'pagebuilder_content_1column');
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'page';
    }

    /**
     * @inheritDoc
     */
    public static function getTitle()
    {
        return __('Page');
    }
}
