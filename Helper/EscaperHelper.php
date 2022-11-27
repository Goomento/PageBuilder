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
 *
 * NOTE: Use these static methods in template hook only - which wrapped in HooksHelper::doAction( 'header' ) or
 * HooksHelper::doAction( 'footer' ) ... . Otherwise might cause some issues with classes loader.
 * See https://developer.adobe.com/commerce/php/development/components/object-manager/#usage-rules
 *
 * @method static escapeHtml($data, $allowedTags = null)
 * @see Escaper::escapeHtml()
 * @method static escapeHtmlAttr($string, $escapeSingleQuote = true)
 * @see Escaper::escapeHtmlAttr()
 * @method static encodeUrlParam($string)
 * @see Escaper::encodeUrlParam()
 * @method static escapeJs($string)
 * @see Escaper::escapeJs()
 * @method static escapeCss($string)
 * @see Escaper::escapeCss()
 * @method static escapeJsQuote($data, $quote = '\'')
 * @see Escaper::escapeJsQuote()
 */
// phpcs:disable Magento2.Functions.StaticFunction.StaticFunction
class EscaperHelper
{
    use TraitStaticInstances;
    use TraitStaticCaller;

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array $data
     * @param bool $escaped
     * @return array
     */
    public static function filter(array $data, bool $escaped = false)
    {
        if ($escaped) {
            return (new \Zend_Filter_Input([], [], $data))->getEscaped();
        } else {
            return (new \Zend_Filter_Input([], [], $data))->getUnescaped();
        }
    }

    /**
     * String to slug type
     *
     * @param $text
     * @param string $divider
     * @return string
     */
    public static function slugify($text, string $divider = '-')
    {
        // replace non letter or digits by divider
        $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, $divider);

        // remove duplicate divider
        $text = preg_replace('~-+~', $divider, $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    /**
     * @return Escaper
     */
    protected static function getStaticInstance()
    {
        return self::getInstance(Escaper::class);
    }
}
