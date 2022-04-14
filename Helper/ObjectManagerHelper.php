<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Managers\Schemes;
use Goomento\PageBuilder\Builder\Managers\Elements;
use Goomento\PageBuilder\Builder\Managers\Tags;
use Goomento\PageBuilder\Builder\Managers\Widgets;
use Goomento\PageBuilder\Builder\Managers\Settings;
use Goomento\PageBuilder\Builder\Managers\Documents;
use Goomento\PageBuilder\Builder\Managers\Sources;
use Magento\Store\Model\StoreManager;

/**
 * Stored the Classes in use to save expense
 * This class's using ObjectManager pattern - which is not encourage by Magento
 * Use this by your own caution
 */
class ObjectManagerHelper extends \Goomento\Core\Helper\ObjectManagerHelper
{
    /**
     * @return Sources
     */
    public static function getSourcesManager() : Sources
    {
        return self::getSubject(Sources::class);
    }

    /**
     * @return Documents
     */
    public static function getDocumentsManager() : Documents
    {
        return self::getSubject(Documents::class);
    }

    /**
     * @return Settings
     */
    public static function getSettingsManager() : Settings
    {
        return self::getSubject(Settings::class);
    }

    /**
     * @return Widgets
     */
    public static function getWidgetsManager() : Widgets
    {
        return self::getSubject(Widgets::class);
    }

    /**
     * @return Controls
     */
    public static function getControlsManager() : Controls
    {
        return self::getSubject(Controls::class);
    }

    /**
     * @return Schemes
     */
    public static function getSchemasManager() : Schemes
    {
        return self::getSubject(Schemes::class);
    }

    /**
     * @return Elements
     */
    public static function getElementsManager() : Elements
    {
        return self::getSubject(Elements::class);
    }

    /**
     * @return Tags
     */
    public static function getTagsManager() : Tags
    {
        return self::getSubject(Tags::class);
    }

    /**
     * @return StoreManager
     */
    public static function getStoresManager() : StoreManager
    {
        return self::getSubject(StoreManager::class);
    }

    /**
     * Saved all instances
     *
     * @var array
     */
    private static $instances = [];

    /**
     * @param string $class
     * @return mixed
     */
    private static function getSubject(string $class)
    {
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = self::get($class);
        }

        return self::$instances[$class];
    }
}
