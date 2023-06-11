<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Goomento\Core\Traits\TraitStaticCaller;
use Goomento\Core\Traits\TraitStaticInstances;
use Magento\Store\Model\ScopeInterface;

/**
 * @see \Goomento\PageBuilder\Helper\Data
 * @method static mixed getConfig($path, $scopeType = ScopeInterface::SCOPE_STORE, $scopeCode = null)
 * @see Data::getConfig()
 * @method static mixed getBuilderConfig($path, $scopeType = ScopeInterface::SCOPE_STORE, $scopeCode = null)
 * @see Data::getBuilderConfig()
 * @method static bool getAllowedDownloadImage() Whether download image or not
 * @see Data::getAllowedDownloadImage()
 * @method static bool isActive() Deprecated Decide to use should be following the WYSIWYG or On/Off option each content
 * @see Data::isActive()
 * @method static string getDownloadFolder()
 * @see Data::getDownloadFolder()
 * @method static bool useInlineCss()
 * @see Data::useInlineCss()
 * @method static string getFbAppId()
 * @see Data::getFbAppId()
 * @method static string getGoogleMapsKey()
 * @see Data::getGoogleMapsKey()
 * @method static bool isLocalFont()
 * @see Data::isLocalFont()
 * @method static bool isDebugMode()
 * @see Data::isDebugMode()
 * @method static bool addResourceGlobally()
 * @see Data::addResourceGlobally()
 * @method static bool isCssMinifyFilesEnabled()
 * @see Data::isCssMinifyFilesEnabled()
 * @method static bool isJsMinifyFilesEnabled()
 * @see Data::isJsMinifyFilesEnabled()
 * @method static string getCustomMediaUrl()
 * @see Data::getCustomMediaUrl()
 * @method static bool isModuleOutputEnabled(string $module)
 * @see Data::isModuleOutputEnabled()Url()
 * @see Data::getCustomMediaUrl()
 * @method static string getConnectorToken()
 * @see Data::getConnectorToken()
 */
// phpcs:disable Magento2.Functions.StaticFunction.StaticFunction
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
     *
     * @param string $handle
     * @param string $jsVar
     * @param mixed $config
     */
    public static function printJsConfig($handle, $jsVar, $config)
    {
        $config = json_encode($config, JSON_INVALID_UTF8_SUBSTITUTE);

        $config = str_replace('}},"', '}},' . PHP_EOL . '"', $config);

        $scriptData = 'var ' . $jsVar . ' = ' . $config . ';';

        ThemeHelper::inlineScript($handle, $scriptData, 'before');
    }

    /**
     * Get placeholder image source.
     *
     * Retrieve the source of the placeholder image.
     *
     *
     * @return string The source of the default placeholder image used by Goomento.
     */
    public static function getPlaceholderImageSrc()
    {
        $placeholderImage = UrlBuilderHelper::getAssetUrlWithParams('Goomento_PageBuilder::images/placeholder.png');

        /**
         * Get placeholder image source.
         *
         * Filters the source of the default placeholder image used by Goomento.
         *
         *
         * @param string $placeholderImage The source of the default placeholder image.
         */
        return HooksHelper::applyFilters('pagebuilder/utils/get_placeholder_image_src', $placeholderImage)->getResult();
    }

    /**
     * Render html attributes
     *
     * @param array $attributes
     *
     * @return string
     *
     * phpcs:ignore Generic.Metrics.NestingLevel.TooHigh
     */
    public static function renderHtmlAttributes(array $attributes)
    {
        $renderedAttributes = [];

        foreach ($attributes as $attributeKey => $attributeValues) {
            if (is_array($attributeValues)) {
                foreach ($attributeValues as &$value) {
                    if (!is_scalar($value)) {
                        if (is_array($value)) {
                            $value = \Goomento\PageBuilder\Helper\DataHelper::encode($value);
                        } elseif (is_object($value) && method_exists($value, '__toString')) {
                            $value = $value->__toString();
                        } else {
                            try {
                                $value = (string) $value;
                            } catch (\Exception $e) {
                                $value = '';
                            }
                        }
                    }
                }
                $attributeValues = implode(' ', $attributeValues);
            }

            $renderedAttributes[] = sprintf(
                '%1$s="%2$s"',
                EscaperHelper::escapeHtmlAttr($attributeKey),
                EscaperHelper::escapeHtmlAttr($attributeValues)
            );
        }

        return implode(' ', $renderedAttributes);
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
     * Compare conditions.
     *
     * Whether the two values comply the comparison operator.
     *
     *
     * @param mixed  $leftValue  First value to compare.
     * @param mixed  $rightValue Second value to compare.
     * @param string $operator    Comparison operator.
     *
     * @return bool Whether the two values complies the comparison operator.
     */
    public static function compare($leftValue, $rightValue, $operator)
    {
        switch ($operator) {
            case '==':
                return $leftValue == $rightValue;
            case '!=':
                return $leftValue != $rightValue;
            case '!==':
                return $leftValue !== $rightValue;
            case 'in':
                return in_array($leftValue, $rightValue, true);
            case '!in':
                return !in_array($leftValue, $rightValue, true);
            case 'contains':
                return in_array($rightValue, $leftValue, true);
            case '!contains':
                return !in_array($rightValue, $leftValue, true);
            case '<':
                return $leftValue < $rightValue;
            case '<=':
                return $leftValue <= $rightValue;
            case '>':
                return $leftValue > $rightValue;
            case '>=':
                return $leftValue >= $rightValue;
            default:
                return $leftValue === $rightValue;
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
        $isOrCondition = isset($conditions['relation']) && 'or' === $conditions['relation'];

        $conditionSucceed = !$isOrCondition;

        foreach ($conditions['terms'] as $term) {
            if (!empty($term['terms'])) {
                $comparisonResult = self::check($term, $comparison);
            } else {
                $value = self::arrayGetValue($comparison, $term['name'], '.');

                $operator = null;

                if (!empty($term['operator'])) {
                    $operator = $term['operator'];
                }

                $comparisonResult = self::compare($value, $term['value'], $operator);
            }

            if ($isOrCondition) {
                if ($comparisonResult) {
                    return true;
                }
            } elseif (!$comparisonResult) {
                return false;
            }
        }

        return $conditionSucceed;
    }

    /**
     * @param string $str
     * @param $separator
     * @return array
     */
    public static function extractStr(string $str, $separator = ',') : array
    {
        $data = explode($separator, $str);
        if (!empty($data)) {
            $data = array_map('trim', $data);
            $data = array_filter($data);
        }

        return $data;
    }

    /**
     * Decodes the given $encodedValue string from JSON
     *
     * @param mixed $encodedValue
     * @param mixed $objectDecodeType
     * @return false|mixed
     * @throws \Exception
     */
    public static function decode($encodedValue, $objectDecodeType = 1)
    {
        $instance = $result = false;
        if (class_exists('Zend_Json')) {
            $instance = 'Zend_Json';
        } elseif (class_exists('Laminas\Json\Json')) {
            $instance = 'Laminas\Json\Json';
        }

        if ($instance) {
            $result = $instance::decode($encodedValue, $objectDecodeType);
        }

        return $result;
    }

    /**
     * Encodes the given $valueToEncode object to JSON
     *
     * @param mixed $valueToEncode
     * @param bool $cycleCheck
     * @param array $options
     * @return false|string
     */
    public static function encode($valueToEncode, bool $cycleCheck = false, array $options = [])
    {
        $instance = $result = false;
        if (class_exists('Zend_Json')) {
            $instance = 'Zend_Json';
        } elseif (class_exists('Laminas\Json\Json')) {
            $instance = 'Laminas\Json\Json';
        }

        if ($instance) {
            $result = $instance::encode($valueToEncode, $cycleCheck, $options);
        }

        return $result;
    }
    /**
     * @inheritDoc
     */
    protected static function getStaticInstance()
    {
        return Data::class;
    }
}
