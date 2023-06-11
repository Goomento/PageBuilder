<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Goomento\Core\Traits\TraitStaticCaller;
use Goomento\Core\Traits\TraitStaticInstances;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;

/**
 *
 * NOTE: Use these static methods in template hook only - which wrapped in HooksHelper::doAction( 'header' ) or
 * HooksHelper::doAction( 'footer' ) ... . Otherwise might cause some issues with classes loader.
 * See https://developer.adobe.com/commerce/php/development/components/object-manager/#usage-rules
 *
 * @method static getParams();
 * @see RequestInterface::getParams();
 * @method static getParam($param, $default = null);
 * @see RequestInterface::getParam();
 * @method static isSecure();
 * @see RequestInterface::isSecure();
 */
// phpcs:disable Magento2.Functions.StaticFunction.StaticFunction
// phpcs:disable Magento2.Security.Superglobal.SuperglobalUsageWarning
class RequestHelper
{
    use TraitStaticCaller;
    use TraitStaticInstances;

    /**
     * @return bool
     */
    public static function isAjax()
    {
        return ((isset($_SERVER['HTTP_X_REQUESTED_WITH'])) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
    }

    /**
     * @return mixed
     */
    protected static function getStaticInstance()
    {
        return RequestInterface::class;
    }

    /**
     * @return mixed
     */
    public static function getAccessToken()
    {
        return self::getParam(EncryptorHelper::ACCESS_TOKEN);
    }

    /**
     * @return DataPersistorInterface
     */
    public static function getDataPersistor() : DataPersistorInterface
    {
        return self::getInstance(DataPersistorInterface::class);
    }

    /**
     * @param string $key
     * @param $data
     * @return void
     */
    public static function setPersistedData(string $key, $data)
    {
        self::getDataPersistor()->set($key, $data);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public static function getPersistedData(string $key)
    {
        return self::getDataPersistor()->get($key);
    }

    /**
     * @param string $key
     * @return void
     */
    public static function clearPersistedData(string $key)
    {
        self::getDataPersistor()->clear($key);
    }

    /**
     * @param string $url
     * @param $query string|array
     * @return string
     */
    public static function appendQueryStringToUrl(string $url, $query): string
    {
        // the query is empty, return the original url straightaway
        if (empty($query)) {
            return $url;
        }

        $parsedUrl = parse_url($url);
        if (empty($parsedUrl['path'])) {
            $url .= '/';
        }

        // if the query is array convert it to string
        $queryString = is_array($query) ? http_build_query($query) : $query;

        // check if there is already any query string in the URL
        if (empty($parsedUrl['query'])) {
            // remove duplications
            parse_str($queryString, $queryStringArray);
            $url .= '?' . http_build_query($queryStringArray);
        } else {
            $queryString = $parsedUrl['query'] . '&' . $queryString;

            // remove duplications
            parse_str($queryString, $queryStringArray);

            // place the updated query in the original query position
            $url = substr_replace($url, http_build_query($queryStringArray), strpos($url, $parsedUrl['query']), strlen($parsedUrl['query']));
        }

        return $url;
    }
}
