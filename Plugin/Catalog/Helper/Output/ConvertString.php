<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Plugin\Catalog\Helper\Output;

use Magento\Catalog\Helper\Output;

/**
 * Class ConvertString
 * @package Goomento\PageBuilder\Plugin\Catalog\Helper\Output
 */
class ConvertString
{
    /**
     * @param Output $output
     * @param $attributeHtml
     * @return string[]
     */
    public function beforeIsDirectivesExists(
        Output $output,
        $attributeHtml
    )
    {
        return [(string) $attributeHtml];
    }
}
