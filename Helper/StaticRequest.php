<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;


use Goomento\Core\Traits\TraitStaticCaller;
use Goomento\Core\Traits\TraitStaticInstances;
use Magento\Framework\App\RequestInterface;

/**
 * Class StaticRequest
 * @package Goomento\PageBuilder\Helper
 * @method static getParams();
 * @method static getParam($param);
 */
class StaticRequest
{
    use TraitStaticCaller;
    use TraitStaticInstances;

    /**
     * @return mixed
     */
    protected static function getStaticInstance()
    {
        return self::getInstance(RequestInterface::class);
    }

    /**
     * @return mixed
     */
    public static function getAccessToken()
    {
        return self::getParam(StaticEncryptor::ACCESS_TOKEN_PARAM);
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
