<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Goomento\PageBuilder\Api\BuildableContentManagementInterface;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Managers\Schemes;
use Goomento\PageBuilder\Builder\Managers\Elements;
use Goomento\PageBuilder\Builder\Managers\Tags;
use Goomento\PageBuilder\Builder\Managers\Widgets;
use Goomento\PageBuilder\Builder\Managers\Settings;
use Goomento\PageBuilder\Builder\Managers\Documents;
use Goomento\PageBuilder\Builder\Managers\Sources;
use Goomento\PageBuilder\Builder\Modules\Editor;
use Goomento\PageBuilder\Builder\Modules\Frontend;
use Magento\Store\Model\StoreManager;

/**
 *
 * NOTE: Use these static methods in template hook only - which wrapped in HooksHelper::doAction( 'header' ) or
 * HooksHelper::doAction( 'footer' ) ... . Otherwise might cause some issues with classes loader.
 * See https://developer.adobe.com/commerce/php/development/components/object-manager/#usage-rules
 *
 */
// phpcs:disable Magento2.Functions.StaticFunction.StaticFunction
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
     * @return Frontend
     */
    public static function getFrontend() : Frontend
    {
        return self::getSubject(Frontend::class);
    }

    /**
     * @return Editor
     */
    public static function getEditor() : Editor
    {
        return self::getSubject(Editor::class);
    }

    /**
     * @return BuildableContentManagementInterface
     */
    public static function getBuildableContentManagement() : BuildableContentManagementInterface
    {
        return self::getSubject(BuildableContentManagementInterface::class);
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
