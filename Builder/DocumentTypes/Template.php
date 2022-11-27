<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\DocumentTypes;

// phpcs:disable Magento2.Functions.StaticFunction.StaticFunction
class Template extends Section
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
