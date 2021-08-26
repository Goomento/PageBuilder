<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Core\DocumentTypes;

/**
 * Class Section
 * @package Goomento\PageBuilder\Core\DocumentTypes
 */
class Section extends Page
{

    public function getName()
    {
        return 'section';
    }


    public static function getTitle()
    {
        return __('Section');
    }
}
