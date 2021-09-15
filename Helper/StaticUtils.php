<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Goomento\Core\Traits\TraitStaticInstances;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;

/**
 * Class StaticUtils
 * @package Goomento\PageBuilder\Helper
 */
class StaticUtils
{
    use TraitStaticInstances;

    /**
     * @return bool
     */
    public static function isAdminhtml() : bool
    {
        $state = self::getInstance(State::class);
        return $state->getAreaCode() === Area::AREA_ADMINHTML;
    }

    /**
     * @return bool
     */
    public static function isAjax()
    {
        return ((isset($_SERVER['HTTP_X_REQUESTED_WITH'])) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
    }

    /**
     * @return bool
     */
    public static function isCli()
    {
        if (defined('STDIN')) {
            return true;
        }
        if (php_sapi_name() === 'cli') {
            return true;
        }
        if (array_key_exists('SHELL', $_ENV)) {
            return true;
        }
        if (empty($_SERVER['REMOTE_ADDR']) and !isset($_SERVER['HTTP_USER_AGENT']) and count($_SERVER['argv']) > 0) {
            return true;
        }
        if (!array_key_exists('REQUEST_METHOD', $_SERVER)) {
            return true;
        }
        return false;
    }

    /**
     * @param $datetime
     * @param false $full
     * @return string
     * @throws \Exception
     */
    public static function timeElapsedString($datetime, bool $full = false)
    {
        $now = new \DateTime;
        $ago = new \DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = [
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        ];

        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        $string = $string ? implode(', ', $string) . ' ago' : 'just now';
        $string .= ' (' . $ago->format('F j, Y, g:i a') . ')';
        return $string;
    }
}
