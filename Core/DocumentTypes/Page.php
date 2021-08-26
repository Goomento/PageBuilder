<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Core\DocumentTypes;

/**
 * Class Page
 * @package Goomento\PageBuilder\Core\DocumentTypes
 */
class Page extends PageBase
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
        return 'page';
    }


    public static function getTitle()
    {
        return __('Page');
    }
}
