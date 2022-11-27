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

    /**
     * @return bool
     */
    public static function isCli()
    {
        if (defined('STDIN')) {
            return true;
        }
        if (php_sapi_name() === 'cli') {
            return true;
        }
        // phpcs:ignore Magento2.Security.Superglobal.SuperglobalUsageError
        if (array_key_exists('SHELL', $_ENV)) {
            return true;
        }
        // phpcs:ignore Magento2.Security.Superglobal.SuperglobalUsageError
        if (empty($_SERVER['REMOTE_ADDR']) and !isset($_SERVER['HTTP_USER_AGENT']) and count($_SERVER['argv']) > 0) {
            return true;
        }
        // phpcs:ignore Magento2.Security.Superglobal.SuperglobalUsageError
        if (!array_key_exists('REQUEST_METHOD', $_SERVER)) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public static function isBuildable()
    {
        return self::isEditorMode()
            || self::isEditorPreviewMode()
            || self::isViewMode();
    }

    /**
     * @return bool
     */
    public static function isEditorPreviewMode()
    {
        $isPreviewMode = HooksHelper::didAction('pagebuilder/preview/index');
        if ($isPreviewMode === false) {
            $actionName = RegistryHelper::registry('current_action_name');
            $isPreviewMode = $actionName === 'pagebuilder_content_editorpreview';
        }

        return $isPreviewMode;
    }

    /**
     * @return bool
     */
    public static function isViewMode()
    {
        $isPreviewMode = HooksHelper::didAction('pagebuilder/view/index');
        if ($isPreviewMode === false) {
            $actionName = RegistryHelper::registry('current_action_name');
            $isPreviewMode = $actionName === 'pagebuilder_content_view';
        }

        return $isPreviewMode;
    }

    /**
     * @return bool
     */
    public static function isEditorMode()
    {
        return HooksHelper::didAction('pagebuilder/editor/index') ||
            HooksHelper::didAction('pagebuilder/editor/render_widget');
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
