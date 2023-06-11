<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Developer;

/**
 *
 * NOTE: Use these static methods in template hook only - which wrapped in HooksHelper::doAction( 'header' ) or
 * HooksHelper::doAction( 'footer' ) ... . Otherwise might cause some issues with classes loader.
 * See https://developer.adobe.com/commerce/php/development/components/object-manager/#usage-rules
 *
 */
// phpcs:disable Magento2.Functions.StaticFunction.StaticFunction
class ThemeHelper extends \Goomento\Core\Helper\ThemeHelper
{
    /**
     * @var ContentInterface[]
     */
    private static $contents = [];

    /**
     * Resources that contains these string will be debuggable
     * @var string
     */
    const DEBUGGABLE_RESOURCES = '/Goomento_PageBuilder\/build/';

    /**
     * Resources that contains these string will applicable for RTL or LTR
     * @var string[]
     */
    const DIRECTION_RESOURCES = '/^Goomento_PageBuilder\/build\/(editor|editor-preview|frontend)(\.css)*$/';

    /**
     * Add body class to theme
     *
     * @param array|string $args
     * @return array
     */
    public static function getBodyClass($args = [])
    {
        $args['goomento-default'] = 'goomento-default';
        $args['goomento-page'] = 'goomento-page';

        if (self::hasContentOnPage()) {
            foreach (self::$contents as $id => $content) {
                $args['gmt-' . $id] = 'gmt-' . $content->getOriginContent()->getId();
            }
        }

        return $args;
    }

    /**
     * @param ContentInterface $content
     * @return void
     */
    public static function registerContentToPage(BuildableContentInterface $content)
    {
        self::$contents[$content->getType() . '-'. $content->getId()] = $content;
    }

    /**
     * Check whether content existed on page
     *
     * @return bool
     */
    public static function hasContentOnPage() : bool
    {
        return !empty(self::$contents);
    }

    /**
     * Hook for render URL
     *
     * @return void
     */
    public static function onStyleLoaderSource($src = '')
    {
        if (!preg_match('#((https?|ftp)://(\S*?\.\S*?))([\s)\[\]{},;"\':<]|\.\s|$)#i', $src)) {
            $src = UrlBuilderHelper::getAssetUrlWithParams($src);
        }

        // Trip off all params in static folder for best fix with Css minification config
        if (strpos($src, 'static/') !== false && StateHelper::isProductionMode()
            && DataHelper::isCssMinifyFilesEnabled()) {
            $src = preg_replace("/^([^?]+).*/", "$1", $src);
        }

        return $src;
    }

    /**
     * @inheritDoc
     */
    public static function registerScript(
        string $handle,
        string $src,
        array $deps = [],
        array $args = []
    ) {
        return parent::registerScript($handle, self::getDebugResource($src), $deps, $args);
    }

    /**
     * @inheritDoc
     */
    public static function registerStyle(
        string $handle,
        $src,
        array $deps = [],
        $ver = false,
        string $media = 'all'
    ) {
        if (DataHelper::isRtl() && preg_match(self::DIRECTION_RESOURCES, $src)) {
            $src = str_replace('.css', '-rtl.css', $src);
        }
        return parent::registerStyle($handle, self::getDebugResource($src), $deps, $ver, $media);
    }

    /**
     * Get debug resource
     *
     * @param string $handle
     * @return void
     */
    private static function getDebugResource(string $handle) : string
    {
        if (!Developer::debug() && preg_match(self::DEBUGGABLE_RESOURCES, $handle)) {
            $parts = explode('.', $handle);
            $ext = array_pop($parts);
            if (in_array($ext, ['js', 'css'])) {
                $parts[] = 'min';
                $parts[] = $ext;
            } else {
                $parts[] = $ext;
                $parts[] = 'min';
            }

            $handle = implode('.', $parts);
        }

        return $handle;
    }
}
