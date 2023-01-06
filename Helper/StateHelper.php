<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Goomento\Core\Traits\TraitStaticCaller;
use Goomento\Core\Traits\TraitStaticInstances;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;

/**
 *
 * NOTE: Use these static methods in template hook only - which wrapped in HooksHelper::doAction( 'header' ) or
 * HooksHelper::doAction( 'footer' ) ... . Otherwise might cause some issues with classes loader.
 * See https://developer.adobe.com/commerce/php/development/components/object-manager/#usage-rules
 *
 * @see \Goomento\Core\Helper\State
 * @method static string getAreaCode()
 * @see State::getAreaCode()
 * @method static string getMode()
 * @see State::getMode()
 */
// phpcs:disable Magento2.Functions.StaticFunction.StaticFunction
// phpcs:disable Magento2.Security.Superglobal.SuperglobalUsageWarning
class StateHelper
{
    use TraitStaticCaller;
    use TraitStaticInstances;

    private static $states = [];

    /**
     * @return bool
     */
    public static function isCli()
    {
        if (!isset(self::$states['cli'])) {
            $cli = false;
            if (defined('STDIN')) {
                $cli = true;
            }
            if (php_sapi_name() === 'cli') {
                $cli = true;
            }
            // phpcs:ignore Magento2.Security.Superglobal.SuperglobalUsageError
            if (array_key_exists('SHELL', $_ENV)) {
                $cli = true;
            }
            // phpcs:ignore Magento2.Security.Superglobal.SuperglobalUsageError
            if (empty($_SERVER['REMOTE_ADDR']) and !isset($_SERVER['HTTP_USER_AGENT']) and count($_SERVER['argv']) > 0) {
                $cli = true;
            }
            // phpcs:ignore Magento2.Security.Superglobal.SuperglobalUsageError
            if (!array_key_exists('REQUEST_METHOD', $_SERVER)) {
                $cli = true;
            }

            self::$states['cli'] = $cli;
        }
        return self::$states['cli'];
    }

    /**
     * @param callable $callback
     * @return mixed
     */
    public static function emulateFrontend(callable $callback)
    {
        try {
            $states = self::$states;
            self::$states = [
                'view' => false,
                'editor' => false,
                'buildable' => false,
                'preview' => false,
            ] + self::$states;
            return self::emulateAreaCode(Area::AREA_FRONTEND, $callback);
        } finally {
            self::$states = $states;
        }
    }

    /**
     * @return bool
     */
    public static function isBuildable()
    {
        if (!isset(self::$states['buildable'])) {
            self::$states['buildable'] = self::isEditorMode()
                || self::isEditorPreviewMode()
                || self::isViewMode();
        }
        return self::$states['buildable'];
    }

    /**
     * @return bool
     */
    public static function isEditorPreviewMode()
    {
        if (!isset(self::$states['preview'])) {
            $isPreviewMode = HooksHelper::didAction('pagebuilder/preview/index');
            if ($isPreviewMode === false) {
                $actionName = RegistryHelper::registry('current_action_name');
                $isPreviewMode = $actionName === 'pagebuilder_content_editorpreview';
            }

            self::$states['preview'] = $isPreviewMode;
        }
        return self::$states['preview'];
    }

    /**
     * @return bool
     */
    public static function isViewMode()
    {
        if (!isset(self::$states['view'])) {
            $viewMode = HooksHelper::didAction('pagebuilder/view/index');
            if ($viewMode === false) {
                $actionName = RegistryHelper::registry('current_action_name');
                $viewMode = $actionName === 'pagebuilder_content_view';
            }

            self::$states['view'] = $viewMode;
        }

        return self::$states['view'];
    }

    /**
     * @return bool
     */
    public static function isEditorMode()
    {
        if (!isset(self::$states['editor'])) {
            self::$states['editor'] = HooksHelper::didAction('pagebuilder/editor/index') ||
                HooksHelper::didAction('pagebuilder/editor/render_widget');
        }
        return self::$states['editor'];
    }

    /**
     * @return bool
     */
    public static function isFrontend()
    {
        return self::getAreaCode() === Area::AREA_FRONTEND;
    }

    /**
     * Whether is Production mode or not
     *
     * @return bool
     */
    public static function isProductionMode()
    {
        return self::getMode() === State::MODE_PRODUCTION;
    }

    /**
     * Whether is Adminhtml or not
     *
     * @return bool
     */
    public static function isAdminhtml(): bool
    {
        return self::getAreaCode() === Area::AREA_ADMINHTML;
    }

    /**
     * @inheritdoc
     */
    protected static function getStaticInstance()
    {
        return State::class;
    }
}
