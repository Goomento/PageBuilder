<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Goomento\Core\Helper\ObjectManager;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Magento\Framework\Encryption\EncryptorInterface;

/**
 * Class StaticEncryptor
 * @package Goomento\PageBuilder\Helper
 */
class StaticEncryptor
{
    const ACCESS_TOKEN_PARAM = 'gmt_token';
    /**
     * @var EncryptorInterface
     */
    private static $encryptor;

    /**
     * @param ContentInterface|int $content
     * @param int $userId
     * @return string
     */
    public static function createAccessToken($content, int $userId = 0)
    {
        if ($content instanceof ContentInterface) {
            $content = $content->getId();
        }
        $data = sprintf('CONTENT__%s__USER__%s__TIME__%s', $content, $userId, time());
        return self::encrypt($data);
    }

    /**
     * @param int $length
     * @return false|string
     */
    public static function uniqueString(int $length = 6)
    {
        return substr(md5(uniqid((string) rand(0, 99), true)), 1, $length);
    }

    /**
     * @param string $token
     * @param ContentInterface|int $content
     * @param int $userId
     * @return bool
     */
    public static function isAllowed(string $token, $content, int $userId = 0)
    {
        $token = self::decrypt($token);
        if (!empty($token)) {
            $token = explode('__', $token);
            $data = [];
            for ($i = 0; ; $i++) {
                if (!isset($token[$i+1])) {
                    break;
                }
                $data[$token[$i]] = $token[$i+1];
                ++$i;
            }

            if ($content instanceof ContentInterface) {
                $content = $content->getId();
            }

            if ($data['CONTENT'] == $content) {
                if ($userId && $userId != $data['USER']) {
                    return false;
                }

                return true;
            }

        }

        return false;
    }

    /**
     * @param string $data
     * @return string
     */
    public static function encrypt(string $data)
    {
        $data = self::getEncryptor()->encrypt($data);
        return base64_encode($data);
    }

    /**
     * @param string $token
     * @return string
     */
    public static function decrypt(string $token)
    {
        $token = base64_decode($token);
        return self::getEncryptor()->decrypt($token);
    }

    /**
     * @return EncryptorInterface|mixed
     */
    private static function getEncryptor()
    {
        if (self::$encryptor === null) {
            self::$encryptor = ObjectManager::get(EncryptorInterface::class);
        }

        return self::$encryptor;
    }
}
