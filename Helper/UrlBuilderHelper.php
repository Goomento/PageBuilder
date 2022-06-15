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
    public static function getEditorPreviewUrl(ContentInterface $content, int $userId = 0)
    {
        return self::getFrontendUrl('pagebuilder/content/editorpreview', [
            'content_id' => $content->getId(),
            'store_id' => self::getStoreId($content),
            '_query' => [
                EncryptorHelper::ACCESS_TOKEN => EncryptorHelper::createAccessToken($content, $userId),
                'layout' => $content->getSetting('layout')
            ]
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
            '_query' => [
                EncryptorHelper::ACCESS_TOKEN => EncryptorHelper::createAccessToken($content, $userId)
            ]
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
                $content = ContentHelper::get((int) $content);
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
            'content_id' => $content->getId()
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
     * @param null $area
     * @return string
     */
    public static function urlStaticBuilder($src, $area = null)
    {
        /** @var Repository $assetRepo */
        $assetRepo = ObjectManagerHelper::get(Repository::class);
        if (null === $area) {
            $area = StateHelper::getAreaCode();
        }
        return $assetRepo->getUrlWithParams($src, ['area' => $area]);
    }
}
