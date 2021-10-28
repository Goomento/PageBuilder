<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\DocumentTypes;

use Goomento\PageBuilder\Builder\Base\AbstractDocumentType;

class Section extends AbstractDocumentType
{
    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'section';
    }

    /**
     * @inheritDoc
     */
    public static function getTitle()
    {
        return __('Section');
    }
}
