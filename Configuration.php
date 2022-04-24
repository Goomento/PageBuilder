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
 */
class Configuration
{
    /**
     * Get current version
     *
     * @return string
     */
    public static function version()
    {
        return (string) Developer::getVar(Developer::VERSION);
    }

    /**
     * Is Debugging
     * Set some resources as develop version (Eg: remove `.min` from URL ...)
     *
     * @return bool
     */
    public static function debug()
    {
        return (bool) Developer::getVar(Developer::DEBUG);
    }

    /**
     * Default Breakpoints
     * In case of has the custom breakpoints, modify this and SCSS breakpoint also
     */
    const DEFAULT_BREAKPOINTS = [
        'xs' => 0,
        'sm' => 480,
        'md' => 768,
        'lg' => 1025,
        'xl' => 1440,
        'xxl' => 1600,
    ];
}
