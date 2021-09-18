<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Goomento\Core\Helper\ObjectManager;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Magento\Framework\Url;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;

/**
 * Class StaticUrlBuilder
 * @package Goomento\PageBuilder\Helper
 */
class StaticUrlBuilder
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
            self::$frontendUrlBuilder = ObjectManager::get(Url::class);
        }

        return self::$frontendUrlBuilder;
    }

    /**
     * @return string
     */
    public static function getAjaxUrl()
    {
        return self::getUrl('pagebuilder/ajax/json');
    }


    /**
     * @return UrlInterface
     */
    private static function getUrlBuilder()
    {
        if (null === self::$urlBuilder) {
            self::$urlBuilder = ObjectManager::get(UrlInterface::class);
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
        return self::getFrontendUrlBuilder()->getUrl($routePath, $routeParams);
    }

    /**
     * @param ContentInterface|int $content
     * @param int $userId
     * @return string
     */
    public static function getContentPreviewUrl($content, int $userId = 0)
    {
        if ($content instanceof ContentInterface) {
            $content = $content->getId();
        }
        return self::getFrontendUrl('pagebuilder/content/preview', [
            'content_id' => $content,
            '_query' => [
                StaticEncryptor::ACCESS_TOKEN_PARAM => StaticEncryptor::createAccessToken($content, $userId)
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
            '_query' => [
                StaticEncryptor::ACCESS_TOKEN_PARAM => StaticEncryptor::createAccessToken($content, $userId)
            ]
        ]);
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
     * @param $src
     * @param null $area
     * @return string
     */
    public static function urlStaticBuilder($src, $area = null)
    {
        /** @var Repository $assetRepo */
        $assetRepo = StaticObjectManager::get(Repository::class);
        if (null === $area) {
            $area = StaticState::getAreaCode();
        }
        return $assetRepo->getUrlWithParams($src, ['area' => $area]);
    }
}
