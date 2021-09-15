<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder;

use Goomento\PageBuilder\Configuration;
use Goomento\PageBuilder\Helper\Hooks;
use Goomento\PageBuilder\Helper\StaticEscaper;
use Goomento\PageBuilder\Helper\StaticUrlBuilder;
use Goomento\PageBuilder\Helper\Theme;

/**
 * Class Utils
 * @package Goomento\PageBuilder\Builder
 */
class Utils
{

    /**
     * Is script debug.
     *
     * Whether script debug is enabled or not.
     *
     *
     * @return bool True if it's a script debug is active, false otherwise.
     */
    public static function isScriptDebug()
    {
        return Configuration::DEBUG;
    }

    /**
     * Get placeholder image source.
     *
     * Retrieve the source of the placeholder image.
     *
     *
     * @return string The source of the default placeholder image used by SagoTheme.
     */
    public static function getPlaceholderImageSrc()
    {
        $placeholder_image = StaticUrlBuilder::urlStaticBuilder('Goomento_PageBuilder::images/placeholder.png');

        /**
         * Get placeholder image source.
         *
         * Filters the source of the default placeholder image used by SagoTheme.
         *
         *
         * @param string $placeholder_image The source of the default placeholder image.
         */
        return Hooks::applyFilters('pagebuilder/utils/get_placeholder_image_src', $placeholder_image);
    }

    /**
     * Generate random string.
     *
     * Returns a string containing a hexadecimal representation of random number.
     *
     *
     * @return string Random string.
     */
    public static function generateRandomString()
    {
        return dechex(rand());
    }


    public static function arrayInject($array, $key, $insert)
    {
        $length = array_search($key, array_keys($array), true) + 1;

        return array_slice($array, 0, $length, true) +
            $insert +
            array_slice($array, $length, null, true);
    }

    /**
     * Render html attributes
     *
     * @param array $attributes
     *
     * @return string
     */
    public static function renderHtmlAttributes(array $attributes)
    {
        $rendered_attributes = [];

        foreach ($attributes as $attribute_key => $attribute_values) {
            if (is_array($attribute_values)) {
                $attribute_values = implode(' ', $attribute_values);
            }

            $rendered_attributes[] = sprintf('%1$s="%2$s"', $attribute_key, StaticEscaper::escapeHtml($attribute_values));
        }

        return implode(' ', $rendered_attributes);
    }

    /**
     *
     * @param string $handle
     * @param string $js_var
     * @param mixed $config
     */
    public static function printJsConfig($handle, $js_var, $config)
    {
        $config = json_encode($config, JSON_INVALID_UTF8_SUBSTITUTE);

        $config = str_replace('}},"', '}},' . PHP_EOL . '"', $config);

        $script_data = 'var ' . $js_var . ' = ' . $config . ';';

        Theme::inlineScript($handle, $script_data, 'before');
    }

    /*
     * Checks a control value for being empty, including a string of '0' not covered by PHP's empty().
     * @param string $control_value
     */
    public static function isEmpty($control_value)
    {
        return '0' !== $control_value && empty($control_value);
    }
}
