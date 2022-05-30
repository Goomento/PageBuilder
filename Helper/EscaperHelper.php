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
 * @method static escapeHtml($data, $allowedTags = null)
 * @method static escapeHtmlAttr($string, $escapeSingleQuote = true)
 * @method static encodeUrlParam($string)
 * @method static escapeJs($string)
 * @method static escapeCss($string)
 * @method static escapeJsQuote($data, $quote = '\'')
 */
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
    static protected function getStaticInstance()
    {
        return self::getInstance(Escaper::class);
    }
}
