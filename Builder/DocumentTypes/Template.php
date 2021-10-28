<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\DocumentTypes;

class Template extends Page
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
}
