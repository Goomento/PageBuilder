<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Goomento\Core\Traits\TraitStaticCaller;
use Goomento\Core\Traits\TraitStaticInstances;
use Magento\Framework\App\Cache\Type\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class StaticConfig
 * @package Goomento\PageBuilder\Helper
 * @method static getThemeOptions()
 * @method static delete($path, $storeId = 0)
 * @method static save($path, $value, $storeId = 0)
 * @method static getValue($path, $storeId = 0)
 * @method static setValue($path, $value, $storeId = 0)
 * @method static deleteValue($path, $value, $storeId = 0)
 */
class StaticConfig
{
    use TraitStaticInstances;
    use TraitStaticCaller;

    /**
     * @param $name
     * @return string
     */
    public static function getOptionXmlPath($name)
    {
        return "theme/options/{$name}";
    }

    /**
     * @param $name
     */
    public static function deleteThemeOption($name)
    {
        self::delete(
            self::getOptionXmlPath($name)
        );
    }

    /**
     * @return mixed|null
     */
    public static function getCustomCss()
    {
        return self::getThemeOption('custom_css', '');
    }

    /**
     * @param $name
     * @param null $default
     * @param int $storeId
     * @return mixed
     */
    public static function getThemeOption($name, $default = null, $storeId = 0)
    {
        $value = self::getValue(
            self::getOptionXmlPath($name),
            $storeId
        );

        if (null === $value) {
            $value = $default;
        }

        return $value;
    }

    /**
     * @param $name
     * @param $value
     */
    public static function updateThemeOption($name, $value)
    {
        self::save(
            self::getOptionXmlPath($name),
            $value
        );
    }

    /**
     * @inheritDoc
     */
    static protected function getStaticInstance()
    {
        return \Goomento\PageBuilder\Model\Config::class;
    }
}
