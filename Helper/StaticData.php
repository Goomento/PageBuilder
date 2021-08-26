<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;


use Goomento\Core\Traits\TraitStaticCaller;
use Goomento\Core\Traits\TraitStaticInstances;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class StaticData
 * @package Goomento\PageBuilder\Helper
 * @method static getConfig($path, $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)
 * @method static getPageBuilderConfig($path, $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)
 * @method static isActive()
 */
class StaticData
{
    use TraitStaticInstances;
    use TraitStaticCaller;

    /**
     * @return bool
     */
    public static function isRtl()
    {
        return (bool) self::getPageBuilderConfig('editor/is_rtl');
    }

    /**
     * @return string
     */
    public static function getCssPrintMethod()
    {
        return (string) self::getPageBuilderConfig('editor/style/css_print_method');
    }

    /**
     * @inheritDoc
     */
    static protected function getStaticInstance()
    {
        return Data::class;
    }
}
