<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Goomento\Core\Traits\TraitStaticCaller;
use Goomento\Core\Traits\TraitStaticInstances;
use Magento\Framework\Escaper;

/**
 * Class StaticEscaper
 * @package Goomento\PageBuilder\Helper
 * @method static escapeHtml($data, $allowedTags = null)
 * @method static escapeHtmlAttr($string, $escapeSingleQuote = true)
 * @method static encodeUrlParam($string)
 * @method static escapeJs($string)
 * @method static escapeCss($string)
 * @method static escapeJsQuote($data, $quote = '\'')
 */
class StaticEscaper
{
    use TraitStaticInstances;
    use TraitStaticCaller;
    /**
     * @return Escaper
     */
    static protected function getStaticInstance()
    {
        return self::getInstance(Escaper::class);
    }
}
