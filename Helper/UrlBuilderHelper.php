<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Goomento\PageBuilder\Api\Data\ContentInterface;
use Magento\Framework\Url;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;

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
    public static function getContentPreviewUrl(ContentInterface $content, int $userId = 0)
    {
        return self::getFrontendUrl('pagebuilder/content/preview', [
            'content_id' => $content->getId(),
            'store_id' => self::getStoreId($content),
            '_query' => [
                EncryptorHelper::ACCESS_TOKEN => EncryptorHelper::createAccessToken($content, $userId),
                'layout' => $content->getSetting('layout')
            ]
        ]);
    }

    /**
     * @param ContentInterface|int $content
     * @param int $userId
     * @return string
     */
    public static function getContentViewUrl($content, int $userId = 0)
    {
        if ($content instanceof ContentInterface) {
            $content = $content->getId();
        }
        return self::getFrontendUrl('pagebuilder/content/view', [
            'content_id' => $content,
            'store_id' => self::getStoreId($content),
            '_query' => [
                EncryptorHelper::ACCESS_TOKEN => EncryptorHelper::createAccessToken($content, $userId)
            ]
        ]);
    }

    /**
     * @param ContentInterface|int $content
     * @return string
     */
    public static function getPublishedContentUrl($content)
    {
        if (!($content instanceof ContentInterface)) {
            $content = ContentHelper::get((int) $content);
        }

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
     * @param ContentInterface|int $content
     * @return string
     */
    public static function getContentExportUrl($content)
    {
        if ($content instanceof ContentInterface) {
            $content = $content->getId();
        }
        return self::getUrl('pagebuilder/content/export', [
            'content_id' => $content
        ]);
    }

    /**
     * @param ContentInterface|int $content
     * @return string
     */
    public static function getContentEditUrl($content)
    {
        if (!($content instanceof ContentInterface)) {
            $content = ContentHelper::get((int) $content);
        }
        return self::getUrl('pagebuilder/content/edit', [
            'type' => $content->getType(),
        ]);
    }

    /**
     * @param ContentInterface|int $content
     * @return string
     */
    public static function getContentDeleteUrl($content)
    {
        if ($content instanceof ContentInterface) {
            $content = $content->getId();
        }
        return self::getUrl('pagebuilder/content/delete', [
            'content_id' => $content,
        ]);
    }

    /**
     * Get Live Editor Url
     *
     * @param ContentInterface|int $content
     * @return string
     */
    public static function getLiveEditorUrl($content)
    {
        if (!($content instanceof ContentInterface)) {
            $content = ContentHelper::get((int) $content);
        }
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
