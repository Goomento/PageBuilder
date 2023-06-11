<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Goomento\Core\Traits\TraitStaticCaller;
use Goomento\Core\Traits\TraitStaticInstances;
use Goomento\PageBuilder\Developer;
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
 * @method static string emulateAreaCode($areaCode, $callback, $params = [])
 * @see State::emulateAreaCode()
 */
// phpcs:disable Magento2.Functions.StaticFunction.StaticFunction
// phpcs:disable Magento2.Security.Superglobal.SuperglobalUsageWarning
// phpcs:disable Magento2.Security.Superglobal.SuperglobalUsageError
class StateHelper
{
    use TraitStaticCaller;
    use TraitStaticInstances;

    /**
     * @return bool
     */
    public static function isCli() : bool
    {
        if (Developer::getVar('is_cli') === null) {
            $cli = false;
            if (defined('STDIN')) {
                $cli = true;
            }
            if (php_sapi_name() === 'cli') {
                $cli = true;
            }
            if (array_key_exists('SHELL', $_ENV)) {
                $cli = true;
            }
            if (empty($_SERVER['REMOTE_ADDR']) and !isset($_SERVER['HTTP_USER_AGENT']) and count($_SERVER['argv']) > 0) {
                $cli = true;
            }
            if (!array_key_exists('REQUEST_METHOD', $_SERVER)) {
                $cli = true;
            }
            Developer::setVar('is_cli', $cli);
        }

        return Developer::getVar('is_cli') ?: false;
    }

    /**
     * @param callable $callback
     * @return mixed
     */
    public static function emulateFrontend(callable $callback)
    {
        $isBuildable = Developer::getVar('is_buildable');
        $isView = Developer::getVar('is_view');
        $isEditor = Developer::getVar('is_editor');
        $isPreview = Developer::getVar('is_canvas');

        try {
            Developer::setVar('is_buildable', false);
            Developer::setVar('is_view', false);
            Developer::setVar('is_editor', false);
            Developer::setVar('is_canvas', false);
            return self::emulateAreaCode(Area::AREA_FRONTEND, $callback);
        } finally {
            Developer::setVar('is_buildable', $isBuildable);
            Developer::setVar('is_view', $isView);
            Developer::setVar('is_editor', $isEditor);
            Developer::setVar('is_canvas', $isPreview);
        }
    }

    /**
     * TRUE when doing build content
     * @return bool
     */
    public static function isBuildable() : bool
    {
        if (Developer::getVar('is_buildable') === null) {
            Developer::setVar('is_buildable', self::isEditorMode()
                || self::isCanvasMode()
                || self::isViewMode());
        }
        return Developer::getVar('is_buildable') ?: false;
    }

    /**
     * TRUE when access the editor preview page (canvas page) (FE)
     * @return bool
     */
    public static function isCanvasMode() : bool
    {
        if (Developer::getVar('is_canvas') === null) {
            $isPreviewMode = HooksHelper::didAction('pagebuilder/preview/index');
            if ($isPreviewMode === false) {
                $actionName = RegistryHelper::registry('current_action_name');
                $isPreviewMode = $actionName === 'pagebuilder_content_canvas';
            }

            Developer::setVar('is_canvas', $isPreviewMode);
        }
        return Developer::getVar('is_canvas') ?: false;
    }

    /**
     * TRUE when viewing the in-build content (FE)
     * @return bool
     */
    public static function isViewMode() : bool
    {
        if (Developer::getVar('is_view') === null) {
            $viewMode = HooksHelper::didAction('pagebuilder/view/index');
            if ($viewMode === false) {
                $actionName = RegistryHelper::registry('current_action_name');
                $viewMode = $actionName === 'pagebuilder_content_view';
            }

            Developer::setVar('is_view', $viewMode);
        }

        return Developer::getVar('is_view') ?: false;
    }

    /**
     * TRUE when rendering widget (FE) & editor page (BE)
     * @return bool
     */
    public static function isEditorMode() : bool
    {
        if (Developer::getVar('is_editor') === null) {
            Developer::setVar('is_editor', HooksHelper::didAction('pagebuilder/editor/index') ||
                HooksHelper::didAction('pagebuilder/editor/render_widget'));
        }
        return Developer::getVar('is_editor') ?: false;
    }

    /**
     * @return bool
     */
    public static function isFrontend() : bool
    {
        return self::getAreaCode() === Area::AREA_FRONTEND;
    }

    /**
     * Whether is Production mode or not
     *
     * @return bool
     */
    public static function isProductionMode() : bool
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
