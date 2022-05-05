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

/**
 * @see \Goomento\Core\Helper\State
 * @method static string getAreaCode()
 * @method static bool isAdminhtml()
 */
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
        if (array_key_exists('SHELL', $_ENV)) {
            return true;
        }
        if (empty($_SERVER['REMOTE_ADDR']) and !isset($_SERVER['HTTP_USER_AGENT']) and count($_SERVER['argv']) > 0) {
            return true;
        }
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
            || self::isPreviewMode()
            || self::isViewMode();
    }

    /**
     * @return bool
     */
    public static function isPreviewMode()
    {
        $isPreviewMode = HooksHelper::didAction('pagebuilder/preview/index');
        if ($isPreviewMode === false) {
            $actionName = RegistryHelper::registry('current_action_name');
            $isPreviewMode = $actionName === 'pagebuilder_content_preview';
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
     * @inheritdoc
     */
    static protected function getStaticInstance()
    {
        return \Goomento\Core\Helper\State::class;
    }
}
