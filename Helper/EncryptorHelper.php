<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Magento\Framework\Encryption\EncryptorInterface;

/**
 *
 * NOTE: Use these static methods in template hook only - which wrapped in HooksHelper::doAction( 'header' ) or
 * HooksHelper::doAction( 'footer' ) ... . Otherwise might cause some issues with classes loader.
 * See https://developer.adobe.com/commerce/php/development/components/object-manager/#usage-rules
 *
 */
// phpcs:disable Magento2.Functions.StaticFunction.StaticFunction
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
     * @param BuildableContentInterface $content
     * @param int|null $userId
     * @param int|null $timeExpired Time expire in second
     * @return string
     */
    public static function createAccessToken(BuildableContentInterface $content, ?int $userId = 0, ?int $timeExpired = self::DEFAULT_EXPIRED_SECONDS)
    {
        $contentId = $content->getOriginContent()->getId();
        $timeExpired = HooksHelper::applyFilters('pagebuilder/encryptor/time_expire', $timeExpired, $content)->getResult();
        $data = sprintf('%s_%s_%s', $contentId, $userId, time() + $timeExpired);
        return self::encrypt($data);
    }

    /**
     * @param int $length
     * @return false|string
     */
    public static function uniqueString(int $length = 6)
    {
        $prefix = (string) rand(0, 99) . rand(0, 999);
        return substr(sha1(uniqid($prefix)), 0, $length);
    }

    /**
     * @param string $str
     * @param int $length
     * @return false|string
     */
    public static function uniqueStringId(string $str, int $length = 6)
    {
        return substr(sha1($str), 0, $length);
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

        $contentId = $content;

        if (!empty($token)) {
            $token = explode('_', $token);
            list($tokenContentId, $tokenUserId, $tokenTime) = $token;

            if (!$tokenTime || $tokenTime < time()) {
                return false;
            }

            if ($content instanceof ContentInterface) {
                $contentId = $content->getOriginContent()->getId();
            }

            if ($tokenContentId == $contentId) {
                if ($userId && $tokenUserId != $userId) {
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
        // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
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
