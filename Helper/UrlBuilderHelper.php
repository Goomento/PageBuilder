<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Magento\Framework\Url;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;

/**
 *
 * NOTE: Use these static methods in template hook only - which wrapped in HooksHelper::doAction( 'header' ) or
 * HooksHelper::doAction( 'footer' ) ... . Otherwise might cause some issues with classes loader.
 * See https://developer.adobe.com/commerce/php/development/components/object-manager/#usage-rules
 *
 */
// phpcs:disable Magento2.Functions.StaticFunction.StaticFunction
class UrlBuilderHelper
{
    /**
     * @var UrlInterface
     */
    private static $urlBuilder;
    /**
     * @var Url
     */
    private static $frontendUrlBuilder;
    /**
     * @var Repository
     */
    private static $assetUrlBuilder;

    /**
     * @return Url
     */
    private static function getFrontendUrlBuilder()
    {
        if (null === self::$frontendUrlBuilder) {
            self::$frontendUrlBuilder = ObjectManagerHelper::get(Url::class);
        }

        return self::$frontendUrlBuilder;
    }

    /**
     * @return UrlInterface
     */
    private static function getUrlBuilder()
    {
        if (null === self::$urlBuilder) {
            self::$urlBuilder = ObjectManagerHelper::get(UrlInterface::class);
        }

        return self::$urlBuilder;
    }

    /**
     * @return Repository
     */
    private static function getAssetUrlBuilder()
    {
        if (null === self::$assetUrlBuilder) {
            self::$assetUrlBuilder = ObjectManagerHelper::get(Repository::class);
        }

        return self::$assetUrlBuilder;
    }

    /**
     * Build url by requested path and parameters
     *
     * @param string|null $routePath
     * @param array|null $routeParams
     * @return  string
     */
    public static function getUrl(string $routePath = null, array $routeParams = null)
    {
        return self::getUrlBuilder()->getUrl($routePath, $routeParams);
    }

    /**
     * Build url by requested path and parameters
     *
     * @param string|null $routePath
     * @param array|null $routeParams
     * @return  string
     */
    public static function getFrontendUrl(string $routePath = null, array $routeParams = null)
    {
        $storeId = 0;
        if (isset($routeParams['store_id'])) {
            $storeId = (int) $routeParams['store_id'];
            unset($routeParams['store_id']);
        }

        if ($storeId !== 0) {
            self::getFrontendUrlBuilder()->setScope($storeId);
            $routeParams['_current'] = false;
            $routeParams['_nosid'] = true;
        }

        return self::getFrontendUrlBuilder()->getUrl($routePath, $routeParams);
    }

    /**
     * @param ContentInterface $content
     * @param int $userId
     * @return string
     */
    public static function getCanvasUrl(ContentInterface $content, int $userId = 0)
    {
        return self::getFrontendUrl('pagebuilder/content/canvas', [
            'content_id' => $content->getId(),
            'store_id' => self::getStoreId($content),
            'layout' => $content->getSetting('layout'),
            EncryptorHelper::ACCESS_TOKEN => EncryptorHelper::createAccessToken($content, $userId)
        ]);
    }

    /**
     * @param BuildableContentInterface $content
     * @param int $userId
     * @return string
     */
    public static function getContentViewUrl(BuildableContentInterface $content, int $userId = 0)
    {
        return self::getFrontendUrl('pagebuilder/content/view', [
            'content_id' => $content->getOriginContent()->getId(),
            'store_id' => self::getStoreId($content),
            EncryptorHelper::ACCESS_TOKEN => EncryptorHelper::createAccessToken($content, $userId)
        ]);
    }

    /**
     * @param ContentInterface $content
     * @return string
     */
    public static function getPublishedContentUrl(ContentInterface $content)
    {
        return self::getFrontendUrl($content->getIdentifier(), [
            'store_id' => self::getStoreId($content)
        ]);
    }

    /**
     * @param $content
     * @return int
     */
    private static function getStoreId($content) : int
    {
        $storeId = RequestHelper::getParam('store');
        if ($storeId === null) {
            $storeId = 0;
            if (!($content instanceof ContentInterface)) {
                $content = BuildableContentHelper::getContent((int) $content);
            }
            $storeIds = $content->getStoreIds();
            if (!empty($storeIds)) {
                sort($storeIds);
                // get last store id of content
                $storeId = array_pop($storeIds);
            }
        }

        return (int) $storeId;
    }

    /**
     * @param ContentInterface $content
     * @return string
     */
    public static function getContentExportUrl(ContentInterface $content)
    {
        return self::getUrl('pagebuilder/content/export', [
            'content_id' => $content->getId(),
            'type' => $content->getType(),
        ]);
    }

    /**
     * @param ContentInterface $content
     * @return string
     */
    public static function getContentEditUrl(ContentInterface $content)
    {
        return self::getUrl('pagebuilder/content/edit', [
            'type' => $content->getType(),
            'content_id' => $content->getId(),
        ]);
    }

    /**
     * @param ContentInterface $content
     * @return string
     */
    public static function getContentDeleteUrl(ContentInterface $content)
    {
        return self::getUrl('pagebuilder/content/delete', [
            'content_id' => $content->getId(),
            'type' => $content->getType(),
        ]);
    }

    /**
     * Get Live Editor Url
     *
     * @param ContentInterface $content
     * @return string
     */
    public static function getLiveEditorUrl(ContentInterface $content)
    {
        return self::getUrl('pagebuilder/content/editor', [
            'content_id' => $content->getId(),
            'store' => self::getStoreId($content),
            'type' => $content->getType()
        ]);
    }

    /**
     * @param $src
     * @param array|null $params
     * @return string
     */
    public static function getAssetUrlWithParams($src, ?array $params = [])
    {
        if (!isset($params['area'])) {
            $params['area'] = StateHelper::getAreaCode();
        }
        return self::getAssetUrlBuilder()->getUrlWithParams($src, $params);
    }
}
