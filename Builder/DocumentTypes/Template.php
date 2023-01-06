<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\DocumentTypes;

use Goomento\PageBuilder\Builder\Base\AbstractDocumentType;

class Template extends AbstractDocumentType
{
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'template';
    }

    /**
     * @inheritDoc
     */
    public static function getTitle()
    {
        return __('Template');
    }

    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        parent::registerControls();
        Section::registerLayoutControl($this, 'pagebuilder_content_fullwidth');
    }
}
