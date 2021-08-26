<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Core\DocumentTypes;

/**
 * Class Template
 * @package Goomento\PageBuilder\Core\DocumentTypes
 */
class Template extends Page
{

    /**
     * @return array|bool[]
     */
    public static function getProperties()
    {
        return parent::getProperties();
    }


    public function getName()
    {
        return 'template';
    }


    public static function getTitle()
    {
        return __('Template');
    }
}
