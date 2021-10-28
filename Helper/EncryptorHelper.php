<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Goomento\PageBuilder\Api\Data\ContentInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class EncryptorHelper
{
    const ACCESS_TOKEN = 'token';

    /**
     * Default token will be expired in 3 hours
     */
    const DEFAULT_EXPIRED_SECONDS = 60 * 60 * 3;

    /**
     * @var EncryptorInterface
     */
    private static $encryptor;

    /**
     * Token will be expired in 3 hours
     * @param null $content
     * @param int|null $userId
     * @param int|null $timeExpired
     * @return string
     */
    public static function createAccessToken($content = null, ?int $userId = 0, ?int $timeExpired = self::DEFAULT_EXPIRED_SECONDS)
    {
        if (empty($content)) {
            $content = RequestHelper::getParam(ContentInterface::CONTENT_ID);
        } elseif ($content instanceof ContentInterface) {
            $content = $content->getId();
        }

        $data = sprintf('c_%s_u_%s_t_%s', $content, $userId, ($timeExpired ? time() + $timeExpired : 0));
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
     * @param string|null $token
     * @param ContentInterface|int $content
     * @param int|null $userId
     * @return bool
     */
    public static function isAllowed(?string $token, $content, ?int $userId = 0)
    {
        if (null === $token) {
            $token = (string) RequestHelper::getParam(self::ACCESS_TOKEN);
        }
        $token = self::decrypt($token);
        if (!empty($token)) {
            $token = explode('_', $token);
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

            $createdTime = (int) $data['t'];
            if (!$createdTime || $createdTime < time()) {
                return false;
            }

            if ($data['c'] == $content) {
                if ($userId && $userId != $data['u']) {
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
            self::$encryptor = ObjectManagerHelper::get(EncryptorInterface::class);
        }

        return self::$encryptor;
    }
}
