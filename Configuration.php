<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder;

/**
 * Class Configuration
 * Stored config of Goomento
 *
 * @package Goomento\PageBuilder
 */
class Configuration
{
    const DEBUG = false;

    const VERSION = 1.0;

    /**
     * @return array
     */
    public static function config()
    {
        return [
            'DEBUG' => self::DEBUG,
            'VERSION' => self::VERSION,
        ];
    }
}
