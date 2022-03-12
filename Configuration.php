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
     * Set some resources as develop version (Eg: remove `.min` from URL ...)
     */
    const DEBUG = false;

    /**
     * The Goomento Page Builder Version - which is set for resource version (Eg: styles, json files)
     */
    const VERSION = '0.2.0';

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
