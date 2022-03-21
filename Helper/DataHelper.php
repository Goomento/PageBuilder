<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;


use Goomento\Core\Traits\TraitStaticCaller;
use Goomento\Core\Traits\TraitStaticInstances;
use Goomento\PageBuilder\Configuration;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * @see \Goomento\PageBuilder\Helper\Data
 * @method static mixed getConfig($path, $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)
 * @method static mixed getBuilderConfig($path, $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)
 * @method static bool getAllowedDownloadImage() Whether download image or not
 * @method static bool isActive()
 * @method static string getDownloadFolder()
 */
class DataHelper
{
    use TraitStaticInstances;
    use TraitStaticCaller;

    /**
     * @return bool
     */
    public static function isRtl(): bool
    {
        return (bool) self::getBuilderConfig('editor/is_rtl');
    }

    /**
     * @return string
     */
    public static function getCssPrintMethod()
    {
        return (string) self::getBuilderConfig('editor/style/css_print_method');
    }

    /**
     * @param array $array
     * @param $path
     * @param $value
     * @param string $delimiter
     */
    public static function arraySetValue(array &$array, $path, $value, string $delimiter = '/')
    {
        if (!is_array($path)) {
            $path = explode($delimiter, (string)$path);
        }

        $ref = &$array;

        foreach ($path as $parent) {
            if (isset($ref) && !is_array($ref)) {
                $ref = [];
            }

            $ref = &$ref[$parent];
        }

        $ref = $value;
    }

    /**
     * @param $array
     * @param $path
     * @param string $delimiter
     */
    public static function arrayUnsetValue(&$array, $path, $delimiter = '/')
    {
        if (!is_array($path)) {
            $path = explode($delimiter, $path);
        }

        $key = array_shift($path);

        if (empty($path)) {
            unset($array[$key]);
        } else {
            self::arrayUnsetValue($array[$key], $path);
        }
    }

    /**
     * @param array $array
     * @param $path
     * @param string $delimiter
     * @return array|mixed|null
     */
    public static function arrayGetValue(array &$array, $path, string $delimiter = '/')
    {
        if (!is_array($path)) {
            $path = explode($delimiter, $path);
        }

        $ref = &$array;

        foreach ((array)$path as $parent) {
            if (is_array($ref) && array_key_exists($parent, $ref)) {
                $ref = &$ref[$parent];
            } else {
                return null;
            }
        }
        return $ref;
    }

    /**
     * @param $datetime
     * @param false $full
     * @return string
     * @throws \Exception
     */
    public static function timeElapsedString($datetime, bool $full = false)
    {
        $now = new \DateTime;
        $ago = new \DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = [
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        ];

        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        $string = $string ? implode(', ', $string) . ' ago' : 'just now';
        $string .= ' (' . $ago->format('F j, Y, g:i a') . ')';
        return $string;
    }

    /**
     * @param $string
     * @return bool
     */
    public static function isJson($string)
    {
        return !(json_decode($string, true) == null);
    }

    /**
     * Inject data to array
     *
     * @param $array
     * @param $key
     * @param $insert
     * @return array
     */
    public static function arrayInject($array, $key, $insert)
    {
        $length = array_search($key, array_keys($array), true) + 1;

        return array_slice($array, 0, $length, true) +
            $insert +
            array_slice($array, $length, null, true);
    }

    /**
     * @param $control_value
     * @return bool
     * @deprecated
     */
    public static function isEmpty($control_value)
    {
        return '0' !== $control_value && empty($control_value);
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

        ThemeHelper::inlineScript($handle, $script_data, 'before');
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
        $placeholder_image = UrlBuilderHelper::urlStaticBuilder('Goomento_PageBuilder::images/placeholder.png');

        /**
         * Get placeholder image source.
         *
         * Filters the source of the default placeholder image used by SagoTheme.
         *
         *
         * @param string $placeholder_image The source of the default placeholder image.
         */
        return HooksHelper::applyFilters('pagebuilder/utils/get_placeholder_image_src', $placeholder_image);
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

            $rendered_attributes[] = sprintf('%1$s="%2$s"', $attribute_key, EscaperHelper::escapeHtml($attribute_values));
        }

        return implode(' ', $rendered_attributes);
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

    /**
     * Is script debug.
     *
     * Whether script debug is enabled or not.
     *
     * Use Configuration::DEBUG instead
     *
     * @return bool True if it's a script debug is active, false otherwise.
     * @deprecated
     */
    public static function isScriptDebug()
    {
        return Configuration::DEBUG;
    }

    /**
     * Compare conditions.
     *
     * Whether the two values comply the comparison operator.
     *
     *
     * @param mixed  $left_value  First value to compare.
     * @param mixed  $right_value Second value to compare.
     * @param string $operator    Comparison operator.
     *
     * @return bool Whether the two values complies the comparison operator.
     */
    public static function compare($left_value, $right_value, $operator)
    {
        switch ($operator) {
            case '==':
                return $left_value == $right_value;
            case '!=':
                return $left_value != $right_value;
            case '!==':
                return $left_value !== $right_value;
            case 'in':
                return in_array($left_value, $right_value, true);
            case '!in':
                return !in_array($left_value, $right_value, true);
            case 'contains':
                return in_array($right_value, $left_value, true);
            case '!contains':
                return !in_array($right_value, $left_value, true);
            case '<':
                return $left_value < $right_value;
            case '<=':
                return $left_value <= $right_value;
            case '>':
                return $left_value > $right_value;
            case '>=':
                return $left_value >= $right_value;
            default:
                return $left_value === $right_value;
        }
    }

    /**
     * Check conditions.
     *
     * Whether the comparison conditions comply.
     *
     *
     * @param array $conditions The conditions to check.
     * @param array $comparison The comparison parameter.
     *
     * @return bool Whether the comparison conditions comply.
     */
    public static function check(array $conditions, array $comparison)
    {
        $is_or_condition = isset($conditions['relation']) && 'or' === $conditions['relation'];

        $condition_succeed = !$is_or_condition;

        foreach ($conditions['terms'] as $term) {
            if (!empty($term['terms'])) {
                $comparison_result = self::check($term, $comparison);
            } else {
                preg_match('/(\w+)(?:\[(\w+)])?/', $term['name'], $parsed_name);

                $value = $comparison[$parsed_name[1]];

                if (!empty($parsed_name[2])) {
                    $value = $value[$parsed_name[2]];
                }

                $operator = null;

                if (!empty($term['operator'])) {
                    $operator = $term['operator'];
                }

                $comparison_result = self::compare($value, $term['value'], $operator);
            }

            if ($is_or_condition) {
                if ($comparison_result) {
                    return true;
                }
            } elseif (!$comparison_result) {
                return false;
            }
        }

        return $condition_succeed;
    }

    /**
     * @inheritDoc
     */
    static protected function getStaticInstance()
    {
        return Data::class;
    }
}
